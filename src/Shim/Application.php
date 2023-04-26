<?php

namespace Silex;

use ArrayAccess;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use EZPZTicketing\BoxOffice\Features\EventBus;
use EZPZTicketing\PhpEngineWebTemplate;
use MyPDO;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Application implements ArrayAccess
{
	private Collection $non_objects;
	private ContainerInterface $container;
	private LoggerInterface $logger;

	public function __construct(
		ContainerInterface $container,
		LoggerInterface $logger,
		RequestStack $requestStack
	)
	{
		$this->container = $container;
		$this->logger = $logger;
		$this->non_objects = new ArrayCollection();

		$this->offsetSet('debug', DEV || DEBUG);
		$this->offsetSet('db.config', $GLOBALS['db_config'] ?? NULL);
		$this->offsetSet('html.page_title', $GLOBALS['_default_page_title'] ?? NULL);
		$this->offsetSet('brand.box_office_name', BOX_OFFICE_NAME ?? NULL);
		$this->offsetSet('brand.instance_name', INSTANCE_NAME ?? NULL);
		$this->offsetSet('swiftmailer.options', [
			'host' => 'localhost',
			'port' => '25',
			'username' => '',
			'password' => '',
			'encryption' => null,
			'auth_mode' => null
		]);
		$this->offsetSet('swiftmailer.use_spool', false);
		$this->offsetSet('db', MyPDO::connect($GLOBALS['db_config'] ?? []));

		$session = $this->getSession($requestStack);
		$this->offsetSet('session', $session);
		$this->offsetSet('session_lazy', $session);

		$this->offsetSet('request', $requestStack->getCurrentRequest());

		$this->offsetSet('eventbus', $this->getEventBus());
		$this->offsetSet('webtemplate', $this->getWebTemplating());
	}

	public function offsetExists($offset): bool
	{
		// $this->logger->info("exists: asked for offset: {$offset}");

		if ($this->container->has($offset))
			return true;

		return $this->non_objects->containsKey($offset);
	}

	public function offsetGet($offset)
	{
		// $this->logger->info("get: asked for offset: {$offset}");

		if ($this->container->has('vbo.' . $offset))
			return $this->container->get('vbo.' . $offset);

		return $this->non_objects->get($offset);
	}

	public function offsetSet($offset, $value): void
	{
		// $this->logger->info("set: asked to set offset: {$offset}");

		if ($this->container->has('vbo.' . $offset))
			return;

		$this->non_objects->set($offset, $value);
	}

	public function offsetUnset($offset): void
	{
		$this->logger->warning("unset: no action taken for offset: {$offset}");
	}

	private function getSession(RequestStack $requestStack): ?SessionInterface
	{
		$request = $requestStack->getCurrentRequest();
		if ($request && $request->hasSession())
		{
			return $request->getSession();
		}

		return new Session(new PhpBridgeSessionStorage());
	}

	private function getWebTemplating(): PhpEngineWebTemplate
	{
		$templating = new PhpEngineWebTemplate($this['html.page_title']);
		$templating->app = $this;
		$templating->db = $this['db'];
		$templating->path = $this['url_generator'];
		$templating->request = $this['request'];
		$templating->session = $this['session'];

		return $templating;
	}

	private function getEventBus(): EventBus
	{
		$bus = new EventBus($this['db'], $this['url_generator']);
		$bus->autoloadEventListeners();

		return $bus;
	}

	public function abort($statusCode, $message = '', array $headers = [])
	{
		throw new HttpException($statusCode, $message, null, $headers);
	}

	public function redirect($url, $status = 302)
	{
		return new RedirectResponse($url, $status);
	}

	public function json($data = [], $status = 200, array $headers = []): Response
	{
		return new JsonResponse($data, $status, $headers);
	}

	public function stream($callback = null, $status = 200, array $headers = [])
	{
		return new StreamedResponse($callback, $status, $headers);
	}

	public function getLogger(): LoggerInterface
	{
		return $this->logger;
	}
}
