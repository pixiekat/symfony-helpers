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
use Twig\Environment as TwigEnvironment;

class AppUtilities implements Interfaces\AppUtilitiesInterface {
  
  private CacheInterface $cache;
  private EntityManagerInterface $entityManager;
  private LoggerInterface $logger;
  private ParameterBagInterface $params;
  private RequestStack $requestStack;
  private TwigEnvironment $twig;
  
  public function __construct(
    CacheInterface $cache,
    EntityManagerInterface $entityManager,
    LoggerInterface $defaultLogger = null,
    ParameterBagInterface $params,
    RequestStack $requestStack,
    TwigEnvironment $twig,
  ) {
    $this->cache = $cache;
    $this->entityManager = $entityManager;
    $this->defaultLogger = $defaultLogger ?: $this->createDefaultLogger();
    $this->params = $params;
    $this->requestStack = $requestStack;
    $this->twig = $twig;
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
  public function getLogger(string $name = 'default', ?string $logPath = null): LoggerInterface {
    if ($name === 'default') {
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
  public function getTwig(): TwigEnvironment {
    return $this->twig;
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
