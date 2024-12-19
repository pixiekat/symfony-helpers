<?php
declare(strict_types=1);
namespace Pixiekat\SymfonyHelpers\Interfaces;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;

interface AppUtilitiesInterface {
  
  /**
   * Defines the default logger name.
   * 
   * @var string DEFAULT_LOGGER_NAME
   */
  public const DEFAULT_LOGGER_NAME = 'default';

  /**
   * Calls getSubscribedServices().
   *
   * @return array
   */
  public static function getSubscribedServices(): array;

  /**
   * Generates a URL from the given route.
   *
   * @param string $route
   * @param array $params
   * @param int $referenceType
   * @return string
   */
  public function generateUrl(string $route, array $params = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string;

  /**
   * Gets the Symfony\Contracts\Cache\CacheInterface instance.
   *
   * @return CacheInterface
   */
  public function getCache(): CacheInterface;
  
  /**
   * Gets the current user.
   *
   * @return User|null
   */
  public function getCurrentUser(): ?User;

  /**
   * Gets the Doctrine\ORM\EntityManagerInterface instance.
   *
   * @return EntityManagerInterface
   */
  public function getEntityManager(): EntityManagerInterface;
  
  /**
   * Gets the Psr\Log\LoggerInterface instance.
   * 
   * @param string $name
   * @param string|null $logPath
   *  The path to the log file. If not provided, the default path is used.
   *
   * @return LoggerInterface
   */
  public function getLogger(string $name = 'default', ?string $logPath = null): LoggerInterface;
  
  /**
   * Gets the Symfony\Component\Mailer\Transport\TransportInterface instance.
   *
   * @return TransportInterface
   */
  public function getMailer(): TransportInterface;

  /**
   * Gets a parameter value.
   *
   * @param string $name
   * @return mixed
   */
  public function getParameter(string $name): mixed;

  /**
   * Gets the Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface instance.
   *
   * @return ParameterBagInterface
   */
  public function getParams(): ParameterBagInterface;
  
  /**
   * Gets the Symfony\Component\HttpFoundation\RequestStack instance.
   *
   * @return RequestStack
   */
  public function getRequest(): RequestStack;

  /**
   * Gets the Symfony\Bundle\SecurityBundle\Security instance.
   * 
   * @return Security
   */
  public function getSecurity(): Security;
  
  /**
   * Gets the Symfony\Contracts\Translation\TranslatorInterface instance.
   *
   * @return TranslatorInterface
   */
  public function getTranslator(): TranslatorInterface;
  
  /**
   * Gets the Twig\Environment instance.
   *
   * @return TwigEnvironment
   */
  public function getTwig(): TwigEnvironment;

  /**
   * Sets a parameter value.
   *
   * @param string $name
   * @param mixed $value
   * @return void
   */
  public function setParameter(string $name, mixed $value): void;
}