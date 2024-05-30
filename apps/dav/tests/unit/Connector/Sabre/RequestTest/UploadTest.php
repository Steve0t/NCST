<?php

/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */
namespace OCA\DAV\Tests\unit\Connector\Sabre\RequestTest;

use OCP\AppFramework\Http;
use OCP\Lock\ILockingProvider;

/**
 * Class UploadTest
 *
 * @group DB
 *
 * @package OCA\DAV\Tests\unit\Connector\Sabre\RequestTest
 */
class UploadTest extends RequestTestCase {
	public function testBasicUpload(): void {
		$user = $this->getUniqueID();
		$view = $this->setupUser($user, 'pass');

		$this->assertFalse($view->file_exists('foo.txt'));
		$response = $this->request($view, $user, 'pass', 'PUT', '/foo.txt', 'asd');

		$this->assertEquals(Http::STATUS_CREATED, $response->getStatus());
		$this->assertTrue($view->file_exists('foo.txt'));
		$this->assertEquals('asd', $view->file_get_contents('foo.txt'));

		$info = $view->getFileInfo('foo.txt');
		$this->assertInstanceOf('\OC\Files\FileInfo', $info);
		$this->assertEquals(3, $info->getSize());
	}

	public function testUploadOverWrite(): void {
		$user = $this->getUniqueID();
		$view = $this->setupUser($user, 'pass');

		$view->file_put_contents('foo.txt', 'foobar');

		$response = $this->request($view, $user, 'pass', 'PUT', '/foo.txt', 'asd');

		$this->assertEquals(Http::STATUS_NO_CONTENT, $response->getStatus());
		$this->assertEquals('asd', $view->file_get_contents('foo.txt'));

		$info = $view->getFileInfo('foo.txt');
		$this->assertInstanceOf('\OC\Files\FileInfo', $info);
		$this->assertEquals(3, $info->getSize());
	}

	public function testUploadOverWriteReadLocked(): void {
		$user = $this->getUniqueID();
		$view = $this->setupUser($user, 'pass');

		$view->file_put_contents('foo.txt', 'bar');

		$view->lockFile('/foo.txt', ILockingProvider::LOCK_SHARED);

		$result = $this->request($view, $user, 'pass', 'PUT', '/foo.txt', 'asd');
		$this->assertEquals(Http::STATUS_LOCKED, $result->getStatus());
	}

	public function testUploadOverWriteWriteLocked(): void {
		$user = $this->getUniqueID();
		$view = $this->setupUser($user, 'pass');
		$this->loginAsUser($user);

		$view->file_put_contents('foo.txt', 'bar');

		$view->lockFile('/foo.txt', ILockingProvider::LOCK_EXCLUSIVE);

		$result = $this->request($view, $user, 'pass', 'PUT', '/foo.txt', 'asd');
		$this->assertEquals(Http::STATUS_LOCKED, $result->getStatus());
	}

	public function testChunkedUpload(): void {
		$user = $this->getUniqueID();
		$view = $this->setupUser($user, 'pass');

		$this->assertFalse($view->file_exists('foo.txt'));
		$response = $this->request($view, $user, 'pass', 'PUT', '/foo.txt-chunking-123-2-0', 'asd', ['OC-Chunked' => '1']);

		$this->assertEquals(201, $response->getStatus());
		$this->assertFalse($view->file_exists('foo.txt'));

		$response = $this->request($view, $user, 'pass', 'PUT', '/foo.txt-chunking-123-2-1', 'bar', ['OC-Chunked' => '1']);

		$this->assertEquals(Http::STATUS_CREATED, $response->getStatus());
		$this->assertTrue($view->file_exists('foo.txt'));

		$this->assertEquals('asdbar', $view->file_get_contents('foo.txt'));

		$info = $view->getFileInfo('foo.txt');
		$this->assertInstanceOf('\OC\Files\FileInfo', $info);
		$this->assertEquals(6, $info->getSize());
	}

	public function testChunkedUploadOverWrite(): void {
		$user = $this->getUniqueID();
		$view = $this->setupUser($user, 'pass');

		$view->file_put_contents('foo.txt', 'bar');
		$response = $this->request($view, $user, 'pass', 'PUT', '/foo.txt-chunking-123-2-0', 'asd', ['OC-Chunked' => '1']);

		$this->assertEquals(Http::STATUS_CREATED, $response->getStatus());
		$this->assertEquals('bar', $view->file_get_contents('foo.txt'));

		$response = $this->request($view, $user, 'pass', 'PUT', '/foo.txt-chunking-123-2-1', 'bar', ['OC-Chunked' => '1']);

		$this->assertEquals(Http::STATUS_CREATED, $response->getStatus());

		$this->assertEquals('asdbar', $view->file_get_contents('foo.txt'));

		$info = $view->getFileInfo('foo.txt');
		$this->assertInstanceOf('\OC\Files\FileInfo', $info);
		$this->assertEquals(6, $info->getSize());
	}

	public function testChunkedUploadOutOfOrder(): void {
		$user = $this->getUniqueID();
		$view = $this->setupUser($user, 'pass');

		$this->assertFalse($view->file_exists('foo.txt'));
		$response = $this->request($view, $user, 'pass', 'PUT', '/foo.txt-chunking-123-2-1', 'bar', ['OC-Chunked' => '1']);

		$this->assertEquals(Http::STATUS_CREATED, $response->getStatus());
		$this->assertFalse($view->file_exists('foo.txt'));

		$response = $this->request($view, $user, 'pass', 'PUT', '/foo.txt-chunking-123-2-0', 'asd', ['OC-Chunked' => '1']);

		$this->assertEquals(201, $response->getStatus());
		$this->assertTrue($view->file_exists('foo.txt'));

		$this->assertEquals('asdbar', $view->file_get_contents('foo.txt'));

		$info = $view->getFileInfo('foo.txt');
		$this->assertInstanceOf('\OC\Files\FileInfo', $info);
		$this->assertEquals(6, $info->getSize());
	}

	public function testChunkedUploadOutOfOrderReadLocked(): void {
		$user = $this->getUniqueID();
		$view = $this->setupUser($user, 'pass');

		$this->assertFalse($view->file_exists('foo.txt'));

		$view->lockFile('/foo.txt', ILockingProvider::LOCK_SHARED);

		try {
			$response = $this->request($view, $user, 'pass', 'PUT', '/foo.txt-chunking-123-2-1', 'bar', ['OC-Chunked' => '1']);
		} catch (\OCA\DAV\Connector\Sabre\Exception\FileLocked $e) {
			$this->fail('Didn\'t expect locked error for the first chunk on read lock');
			return;
		}

		$this->assertEquals(Http::STATUS_CREATED, $response->getStatus());
		$this->assertFalse($view->file_exists('foo.txt'));

		// last chunk should trigger the locked error since it tries to assemble
		$result = $this->request($view, $user, 'pass', 'PUT', '/foo.txt-chunking-123-2-0', 'asd', ['OC-Chunked' => '1']);
		$this->assertEquals(Http::STATUS_LOCKED, $result->getStatus());
	}

	public function testChunkedUploadOutOfOrderWriteLocked(): void {
		$user = $this->getUniqueID();
		$view = $this->setupUser($user, 'pass');

		$this->assertFalse($view->file_exists('foo.txt'));

		$view->lockFile('/foo.txt', ILockingProvider::LOCK_EXCLUSIVE);

		try {
			$response = $this->request($view, $user, 'pass', 'PUT', '/foo.txt-chunking-123-2-1', 'bar', ['OC-Chunked' => '1']);
		} catch (\OCA\DAV\Connector\Sabre\Exception\FileLocked $e) {
			$this->fail('Didn\'t expect locked error for the first chunk on write lock'); // maybe forbid this in the future for write locks only?
			return;
		}

		$this->assertEquals(Http::STATUS_CREATED, $response->getStatus());
		$this->assertFalse($view->file_exists('foo.txt'));

		// last chunk should trigger the locked error since it tries to assemble
		$result = $this->request($view, $user, 'pass', 'PUT', '/foo.txt-chunking-123-2-0', 'asd', ['OC-Chunked' => '1']);
		$this->assertEquals(Http::STATUS_LOCKED, $result->getStatus());
	}
}
