<?php
declare(strict_types=1);
namespace Pixiekat\SymfonyHelpers\Interfaces;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment as TwigEnvironment;

interface AppUtilitiesInterface {
  
  /**
   * Gets the Symfony\Contracts\Cache\CacheInterface instance.
   *
   * @return CacheInterface
   */
  public function getCache(): CacheInterface;
  
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
   * Gets the Twig\Environment instance.
   *
   * @return TwigEnvironment
   */
  public function getTwig(): TwigEnvironment;
}