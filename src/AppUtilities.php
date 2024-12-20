<?php
declare(strict_types=1);
namespace Pixiekat\SymfonyHelpers;

use Pixiekat\SymfonyHelpers\Interfaces;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Proxy\Proxy;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
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
   * The Symfony\Component\Mailer\Transport\TransportInterface definition.
   * 
   * @var \Symfony\Component\Mailer\Transport\TransportInterface $mailer
   */
  private TransportInterface $mailer;

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
   * The Symfony\Bundle\SecurityBundle\Security definition.
   * 
   * @var \Symfony\Bundle\SecurityBundle\Security $security
   */
  private Security $security;

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
    TransportInterface $mailer,
    ParameterBagInterface $params,
    RequestStack $requestStack,
    Security $security,
    TranslatorInterface $translator,
    TwigEnvironment $twig,
    UrlGeneratorInterface $urlGenerator,
  ) {
    $this->cache = $cache;
    $this->entityManager = $entityManager;
    $this->defaultLogger = $defaultLogger ?: $this->createDefaultLogger();
    $this->mailer = $mailer;
    $this->params = $params;
    $this->requestStack = $requestStack;
    $this->security = $security;
    $this->translator = $translator;
    $this->twig = $twig;
    $this->urlGenerator = $urlGenerator;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedServices(): array {
    return [
      'cache' => CacheInterface::class,
      'entityManager' => EntityManagerInterface::class,
      'logger' => LoggerInterface::class,
      'mailer' => TransportInterface::class,
      'params' => ParameterBagInterface::class,
      'security' => Security::class,
      'translator' => TranslatorInterface::class,
      'twig' => TwigEnvironment::class,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function arrayToCsv(array $data, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): string {
    return $this->getCsvFromArray($data, $delimiter, $enclosure, $escape);
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
  public function getAppLogger(): LoggerInterface {
    return $this->getLogger('default')->withName('app');
  }

  /**
   * {@inheritdoc}
   */
  public function getAuditLogger(): LoggerInterface {
    return $this->getLogger('default')->withName('audit');
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
  public function getCsvFromArray(array $data, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): string {
    $output = fopen('php://temp', 'r+');
    foreach ($data as $row) {
      fputcsv($output, $row, $delimiter, $enclosure, $escape);
    }
    rewind($output);
    $csv = stream_get_contents($output);
    fclose($output);
    return $csv;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentUser(): ?UserInterface {
    return $this->security->getUser() instanceof UserInterface ? $this->security->getUser() : null;
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
  public function getMailer(): TransportInterface {
    return $this->mailer;
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
  public function getSecurity(): Security {
    return $this->security;
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
  public function initializeObject(object $object): void {
    if ($object instanceof Proxy) {
      $object->__load();
      $this->getEntityManager()->initializeObject($object);
    }
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
