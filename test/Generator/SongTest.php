<?php
/**
 * @copyright 2014-2018 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\AccessorGenerator\Generator;

use Doctrine\Common\Collections\Collection;
use Hostnet\Component\AccessorGenerator\Generator\fixtures\Genre;
use Hostnet\Component\AccessorGenerator\Generator\fixtures\Song;
use PHPUnit\Framework\TestCase;

class SongTest extends TestCase
{
    public function testGetGenres(): void
    {
        $song   = new Song();
        $genres = $song->getGenres();
        self::assertEmpty($genres);
        self::assertInstanceOf(Collection::class, $genres);
    }

    public function testGetGenresTooManyArguments(): void
    {
        $song = new Song();

        $this->expectException(\BadMethodCallException::class);

        $song->getGenres(1);
    }

    /**
     * @depends testGetGenres
     */
    public function testAddGenre(): void
    {
        $radar_love = new Song();
        $help       = new Song();

        $rock = new Genre();
        $jazz = new Genre();

        // Add and receive a genre
        $radar_love->addGenre($rock);
        self::assertSame($rock, $radar_love->getGenres()->first());
        self::assertCount(1, $radar_love->getGenres());

        // Test if we got a reference
        $genres = $radar_love->getGenres();

        // Add the same genre again, we expect no error
        // but also no duplicate entries.
        $radar_love->addGenre($rock);
        self::assertSame($rock, $genres->first());
        self::assertCount(1, $genres);

        // Add the same genre again, we expect no error
        // but also no duplicate entries.
        $radar_love->addGenre($jazz);
        self::assertSame($jazz, $genres->last());
        self::assertCount(2, $genres);

        // Add same genres to multiple songs
        $help->addGenre($rock);
        $help->addGenre($jazz);
        self::assertEquals([$rock, $jazz], $help->getGenres()->toArray());
    }

    public function testAddGenreTooManyArguments(): void
    {
        $song  = new Song();
        $genre = new Genre();

        $this->expectException(\BadMethodCallException::class);

        $song->addGenre($genre, 2);
    }

    /**
     * @depends testGetGenres
     * @depends testAddGenre
     */
    public function testRemoveGenre(): void
    {
        $song  = new Song();
        $genre = new Genre();

        // The initial list should be empty.
        self::assertEmpty($song->getGenres());

        // Add and receive a genre.
        $song->addGenre($genre);
        self::assertSame($genre, $song->getGenres()->first());
        self::assertEquals(1, $song->getGenres()->count());

        // Remove genre, check return value and check list.
        self::assertSame($song->removeGenre($genre), $song);
        self::assertEquals(0, $song->getGenres()->count());

        // Remove not existing genre, check return value. No
        // error is expected.
        self::assertSame($song->removeGenre($genre), $song);
    }

    public function testRemoveGenreTooManyArguments(): void
    {
        $song  = new Song();
        $genre = new Genre();

        $this->expectException(\BadMethodCallException::class);

        $song->removeGenre($genre, 2);
    }
}
