<?php

namespace AlexSkrypnyk\Tests\Unit;

use AlexSkrypnyk\ShellVariablesExtractor\Config\Config;
use AlexSkrypnyk\ShellVariablesExtractor\Factory\AutodiscoveryFactory;

/**
 * Class AutodiscoveryFactoryUnitTest.
 *
 * Unit tests for theAutodiscoveryFactory class.
 */
class AutodiscoveryFactoryUnitTest extends UnitTestBase {

  /**
   * Test that the autodiscovery can discover only items set to be discovered.
   */
  public function testDiscovery() {
    $autodiscovery = new AutodiscoveryFactory('tests/Fixtures');
    $discovered_classes = $autodiscovery->getEntityClasses();
    $this->assertEquals([
      'DummyDiscoverable11' => 'AlexSkrypnyk\Tests\Fixtures\Discovery1\DummyDiscoverable11',
      'DummyDiscoverable12' => 'AlexSkrypnyk\Tests\Fixtures\Discovery1\DummyDiscoverable12',
      'DummyDiscoverable21' => 'AlexSkrypnyk\Tests\Fixtures\Discovery2\DummyDiscoverable21',
      'DummyDiscoverable22' => 'AlexSkrypnyk\Tests\Fixtures\Discovery2\DummyDiscoverable22',
    ], $discovered_classes);
  }

  /**
   * Test that the autodiscovery can discover only items of a certain type.
   */
  public function testDiscoveryTyped() {
    $autodiscovery = new AutodiscoveryFactory('tests/Fixtures/Discovery1');
    $discovered_classes = $autodiscovery->getEntityClasses();
    $this->assertEquals([
      'DummyDiscoverable11' => 'AlexSkrypnyk\Tests\Fixtures\Discovery1\DummyDiscoverable11',
      'DummyDiscoverable12' => 'AlexSkrypnyk\Tests\Fixtures\Discovery1\DummyDiscoverable12',
    ], $discovered_classes);

    $autodiscovery = new AutodiscoveryFactory('tests/Fixtures/Discovery2');
    $discovered_classes = $autodiscovery->getEntityClasses();
    $this->assertEquals([
      'DummyDiscoverable21' => 'AlexSkrypnyk\Tests\Fixtures\Discovery2\DummyDiscoverable21',
      'DummyDiscoverable22' => 'AlexSkrypnyk\Tests\Fixtures\Discovery2\DummyDiscoverable22',
    ], $discovered_classes);
  }

  /**
   * Test creating a single auto discovered entity.
   */
  public function testCreate() {
    $autodiscovery = new AutodiscoveryFactory('tests/Fixtures');
    $discovered = $autodiscovery->create('DummyDiscoverable11', new Config());
    $this->assertEquals('DummyDiscoverable11', $discovered::getName());
    $discovered = $autodiscovery->create('DummyDiscoverable12', new Config());
    $this->assertEquals('DummyDiscoverable12', $discovered::getName());
  }

  /**
   * Test creating all auto discovered entities.
   */
  public function testCreateAll() {
    $config = new Config();
    $autodiscovery = new AutodiscoveryFactory('tests/Fixtures');
    $discovered_all = $autodiscovery->createAll($config);
    $this->assertCount(4, $discovered_all);
    usort($discovered_all, function ($a, $b) {
      return strcmp($a::getName(), $b::getName());
    });
    $this->assertEquals('DummyDiscoverable11', $discovered_all[0]::getName());
    $this->assertEquals('DummyDiscoverable12', $discovered_all[1]::getName());
    $this->assertEquals('DummyDiscoverable21', $discovered_all[2]::getName());
    $this->assertEquals('DummyDiscoverable22', $discovered_all[3]::getName());
  }

  /**
   * Test that exception is thrown when an invalid autodiscovery is requested.
   */
  public function testException() {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Invalid entity: non-existent');
    $autodiscovery = new AutodiscoveryFactory('tests/Fixtures');
    $autodiscovery->create('non-existent', new Config());
  }

}
