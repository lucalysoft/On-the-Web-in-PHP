<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace SuiteCRM\Search;

use Mockery;
use ReflectionException;
use SuiteCRM\Search\Exceptions\MasterSearchEngineNotFoundException;

/**
 * Created by PhpStorm.
 * User: viocolano
 * Date: 21/06/18
 * Time: 16:48
 */
class MasterSearchTest extends SearchTestAbstract
{
    public function testFetchEngine()
    {
        $search = new MasterSearch();

        try {
            $SearchEngine = $this->invokeMethod($search, 'fetchEngine', ['ElasticSearchEngine']);
            $this->assertInstanceOf('ElasticSearchEngine', $SearchEngine);
        } catch (ReflectionException $e) {
            $this->fail("Failed to use reflection!");
        }
    }

    public function testFetchEngineNonExisting()
    {
        $search = new MasterSearch();
        $this->setValue($search, 'CUSTOM_ENGINES_PATH', __DIR__ . '/TestCustomEngines/');

        try {
            $this->invokeMethod($search, 'fetchEngine', ['VeryFakeEngine']);
            $this->fail("Exception should be thrown here!");
        } catch (ReflectionException $e) {
            $this->fail("Failed to use reflection!");
        } catch (MasterSearchEngineNotFoundException $e) {
            // All good!
        }
    }

    public function testFetchEngineCustom()
    {
        $search = new MasterSearch();
        $this->setValue($search, 'CUSTOM_ENGINES_PATH', __DIR__ . '/TestCustomEngines/');

        $engine = $this->invokeMethod($search, 'fetchEngine', ['MockSearch']);

        self::assertInstanceOf(SearchEngine::class, $engine);
    }

    public function testFetchEngineCustomBad()
    {
        $search = new MasterSearch();
        $this->setValue($search, 'CUSTOM_ENGINES_PATH', __DIR__ . '/TestCustomEngines/');

        try {
            $this->invokeMethod($search, 'fetchEngine', ['BadMockSearch']);
            $this->fail("Exception should be thrown here!");
        } catch (MasterSearchEngineNotFoundException $e) {
            echo $e->getMessage();
        }
    }

    public function testGetEngines()
    {
        $expected = [
            0 => 'ElasticSearchEngine',
            1 => 'BadMockSearch',
            2 => 'MockSearch',
        ];
        $actual = MasterSearch::getEngines();

        self::assertEquals($actual, $expected);
    }

    public function testSearchAndViewCustom()
    {
        $search = new MasterSearch();
        $this->setValue($search, 'CUSTOM_ENGINES_PATH', __DIR__ . '/TestCustomEngines/');

        $query = SearchQuery::fromString('bar', null, null, 'MockSearch');

        ob_start();
        $search::searchAndView($query);
        $output = ob_get_flush();

        self::assertEquals('bar', $output);
    }

    public function testSearchAndViewBuiltIn()
    {
        MasterSearch::addEngine('SearchEngineMock', 'tests/unit/lib/SuiteCRM/Search/SearchEngineMock.php');

        $query = SearchQuery::fromString('foo', null, null, 'SearchEngineMock');

        ob_start();
        MasterSearch::searchAndView($query);
        $output = ob_get_flush();

        self::assertEquals('bar', $output);
    }

    public function testFakeSearch()
    {
        MasterSearch::addEngine('SearchEngineMock', 'tests/unit/lib/SuiteCRM/Search/SearchEngineMock.php');

        $result = MasterSearch::search('SearchEngineMock', SearchQuery::fromString('foo'));

        self::assertEquals('bar', $result, "Wrong mocked search result!");

        $result = MasterSearch::search('SearchEngineMock', SearchQuery::fromString('fooz'));

        self::assertEquals('barz', $result, "Wrong mocked search result!");
    }

    public function testSearch2()
    {
        // this time try passing a custom engine
        $mockEngine = Mockery::mock(SearchEngine::class);
        $query = SearchQuery::fromString("Test");

        $mockEngine
            ->shouldReceive('search')
            ->once()
            ->with($query);

        MasterSearch::search($mockEngine, $query);

        Mockery::close();
    }

    public function testSearch3()
    {
        // this time check if the validation works

        $mockEngine = Mockery::mock(MasterSearch::class); // just an object that shouldn't be passed
        $query = SearchQuery::fromString("Test");

        try {
            MasterSearch::search($mockEngine, $query);
            self::fail("Exception should have been thrown!");
        } catch (MasterSearchEngineNotFoundException $e) {
            // All good!
        }

        Mockery::close();
    }
}
