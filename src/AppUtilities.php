<?php
declare(strict_types=1);
namespace Pixiekat\SymfonyHelpers;

use Pixiekat\SymfonyHelpers\Interfaces;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;

class AppUtilities implements Interfaces\AppUtilitiesInterface {
  
  /**
   * The Symfony\Contracts\Cache\CacheInterface definition.
   *
   * @var \Symfony\Contracts\Cache\CacheInterface $cache
   */
  private CacheInterface $cache;

  /**
   * The Doctrine\ORM\EntityManagerInterface definition.
   *
   * @var \Doctrine\ORM\EntityManagerInterface $entityManager
   */
  private EntityManagerInterface $entityManager;

  /**
   * The Psr\Log\LoggerInterface definition.
   *
   * @var \Psr\Log\LoggerInterface $defaultLogger
   */
  private ?LoggerInterface $defaultLogger = null;

  /**
   * The Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface definition.
   *
   * @var \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $params
   */
  private ParameterBagInterface $params;

  /**
   * The Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack $requestStack
   */
  private RequestStack $requestStack;

  /**
   * The Symfony\Contracts\Translation\TranslatorInterface definition.
   * 
   * @var \Symfony\Contracts\Translation\TranslatorInterface $translator
   */
  private TranslatorInterface $translator;

  /**
   * The Twig\Environment definition.
   *
   * @var \Twig\Environment $twig
   */
  private TwigEnvironment $twig;

  /**
   * The Symfony\Component\Routing\Generator\UrlGeneratorInterface definition.
   * 
   * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
   */
  private UrlGeneratorInterface $urlGenerator;
  
  public function __construct(
    CacheInterface $cache,
    EntityManagerInterface $entityManager,
    LoggerInterface $defaultLogger = null,
    ParameterBagInterface $params,
    RequestStack $requestStack,
    TranslatorInterface $translator,
    TwigEnvironment $twig,
    UrlGeneratorInterface $urlGenerator,
  ) {
    $this->cache = $cache;
    $this->entityManager = $entityManager;
    $this->defaultLogger = $defaultLogger ?: $this->createDefaultLogger();
    $this->params = $params;
    $this->requestStack = $requestStack;
    $this->translator = $translator;
    $this->twig = $twig;
    $this->urlGenerator = $urlGenerator;
  }

  /**
   * {@inheritdoc}
   */
  public function generateUrl(string $route, array $params = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string {
    return $this->urlGenerator->generate($route, $params, $referenceType);
  }

  /**
   * {@inheritdoc}
   */
  public function getCache(): CacheInterface {
    return $this->cache;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityManager(): EntityManagerInterface {
    return $this->entityManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getLogger(string $name = self::DEFAULT_LOGGER_NAME, ?string $logPath = null): LoggerInterface {
    if ($name === self::DEFAULT_LOGGER_NAME) {
      return $this->defaultLogger;
    }
    if (is_null($logPath) || empty($logPath)) {
      $logPath = __DIR__ . "/../../var/log/{$name}.log";
    }
    return $this->createLogger($name, $logPath);
  }

  /**
   * {@inheritdoc}
   */
  public function getParameter(string $name): mixed {
    return $this->params->get($name);
  }

  /**
   * {@inheritdoc}
   */
  public function getParams(): ParameterBagInterface {
    return $this->params;
  }

  /**
   * {@inheritdoc}
   */
  public function getRequest(): RequestStack {
    return $this->requestStack;
  }

  /**
   * {@inheritdoc}
   */
  public function getTranslator(): TranslatorInterface {
    return $this->translator;
  }

  /**
   * {@inheritdoc}
   */
  public function getTwig(): TwigEnvironment {
    return $this->twig;
  }

  /**
   * {@inheritdoc}
   */
  public function setParameter(string $name, mixed $value): void {
    $this->params->set($name, $value);
  }

  /**
   * Creates a default LoggerInterface instance.
   *
   * @return LoggerInterface
   */
  private function createDefaultLogger(): LoggerInterface {
    $logger = new Logger('default');
    $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
    return $logger;
  }
  
  /**
   * Creates a new LoggerInterface instance.
   *
   * @param string $name
   * @param string $logPath
   * @return LoggerInterface
   */
  private function createLogger(string $name, string $logPath): LoggerInterface {
    $logger = new Logger($name);
    $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
    return $logger;
  }
}
