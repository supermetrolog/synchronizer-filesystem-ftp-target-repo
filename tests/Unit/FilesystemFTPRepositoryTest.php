<?php

declare(strict_types=1);

namespace tests\unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Supermetrolog\Synchronizer\interfaces\FileInterface;
use Supermetrolog\SynchronizerFilesystemFTPTargetRepo\FilesystemFTPRepository;
use Supermetrolog\SynchronizerFilesystemFTPTargetRepo\FTPFilesystem;

class FilesystemFTPRepositoryTest extends TestCase
{
    private FTPFilesystem $filesystem;

    public function setUp(): void
    {
        $this->filesystem = $this->createMock(FTPFilesystem::class);
    }
    public function testFileExistDirExist(): void
    {
        /** @var MockObject $fl */
        $fl = $this->filesystem;
        $fl->expects($this->once())->method("directoryExists")->willReturn(true);

        $repo = new FilesystemFTPRepository($this->filesystem);

        /** @var MockObject $fileMock */
        $fileMock = $this->createMock(FileInterface::class);
        $fileMock->expects($this->once())->method('isDir')->willReturn(true);

        /** @var FileInterface $fileMock */
        $this->assertTrue($repo->fileExist($fileMock));
    }

    public function testFileExistDirNotExist(): void
    {
        /** @var MockObject $fl */
        $fl = $this->filesystem;
        $fl->expects($this->once())->method("directoryExists")->willReturn(false);

        $repo = new FilesystemFTPRepository($this->filesystem);

        /** @var MockObject $fileMock */
        $fileMock = $this->createMock(FileInterface::class);
        $fileMock->expects($this->once())->method('isDir')->willReturn(true);

        /** @var FileInterface $fileMock */
        $this->assertFalse($repo->fileExist($fileMock));
    }

    public function testFileExistFileExist(): void
    {
        /** @var MockObject $fl */
        $fl = $this->filesystem;
        $fl->expects($this->once())
            ->method("fileExists")
            ->with("/folder/folder2")
            ->willReturn(true);

        $repo = new FilesystemFTPRepository($this->filesystem);

        /** @var MockObject $fileMock */
        $fileMock = $this->createMock(FileInterface::class);
        $fileMock->expects($this->once())->method('isDir')->willReturn(false);
        $fileMock->expects($this->once())
            ->method('getUniqueName')
            ->willReturn("/folder/folder2");

        /** @var FileInterface $fileMock */
        $this->assertTrue($repo->fileExist($fileMock));
    }

    public function testFileExistFileNotExist(): void
    {

        /** @var MockObject $fl */
        $fl = $this->filesystem;
        $fl->expects($this->once())
            ->method("fileExists")
            ->with("/folder/file.txt")
            ->willReturn(false);

        $repo = new FilesystemFTPRepository($this->filesystem);

        /** @var MockObject $fileMock */
        $fileMock = $this->createMock(FileInterface::class);
        $fileMock->expects($this->once())->method('isDir')->willReturn(false);
        $fileMock->expects($this->once())
            ->method('getUniqueName')
            ->willReturn("/folder/file.txt");

        /** @var FileInterface $fileMock */
        $this->assertFalse($repo->fileExist($fileMock));
    }

    public function testRemoveDir(): void
    {
        /** @var MockObject $fl */
        $fl = $this->filesystem;
        $fl->expects($this->once())->method("deleteDirectory")->with("/folder/folder2");

        $repo = new FilesystemFTPRepository($this->filesystem);

        /** @var MockObject $fileMock */
        $fileMock = $this->createMock(FileInterface::class);
        $fileMock->expects($this->once())->method('isDir')->willReturn(true);
        $fileMock->expects($this->once())
            ->method('isDir')
            ->willReturn(true);
        $fileMock->expects($this->once())
            ->method('getUniqueName')
            ->willReturn("/folder/folder2");

        /** @var FileInterface $fileMock */
        $this->assertTrue($repo->remove($fileMock));
    }

    public function testRemoveFile(): void
    {
        /** @var MockObject $fl */
        $fl = $this->filesystem;
        $fl->expects($this->once())->method("delete")->with("/folder/file.txt");

        $repo = new FilesystemFTPRepository($this->filesystem);

        /** @var MockObject $fileMock */
        $fileMock = $this->createMock(FileInterface::class);
        $fileMock->expects($this->once())->method('isDir')->willReturn(false);
        $fileMock->expects($this->once())
            ->method('getUniqueName')
            ->willReturn("/folder/file.txt");

        /** @var FileInterface $fileMock */
        $this->assertTrue($repo->remove($fileMock));
    }

    public function testCreateDir(): void
    {
        /** @var MockObject $fl */
        $fl = $this->filesystem;
        $fl->expects($this->once())->method("createDirectory")->with("/folder/folder2");

        $repo = new FilesystemFTPRepository($this->filesystem);

        /** @var MockObject $fileMock */
        $fileMock = $this->createMock(FileInterface::class);
        $fileMock->expects($this->once())->method('isDir')->willReturn(true);
        $fileMock->expects($this->once())
            ->method('getUniqueName')
            ->willReturn("/folder/folder2");

        /** @var FileInterface $fileMock */
        $this->assertTrue($repo->create($fileMock, null));
    }

    public function testCreateFile(): void
    {
        /** @var MockObject $fl */
        $fl = $this->filesystem;
        $fl->expects($this->once())->method("write")
            ->with("/folder/file.txt", "content content");

        $repo = new FilesystemFTPRepository($this->filesystem);

        /** @var MockObject $fileMock */
        $fileMock = $this->createMock(FileInterface::class);
        $fileMock->expects($this->once())->method('isDir')->willReturn(false);
        $fileMock->expects($this->once())
            ->method('getUniqueName')
            ->willReturn("/folder/file.txt");

        /** @var FileInterface $fileMock */
        $this->assertTrue($repo->create($fileMock, "content content"));
    }

    public function testUpdateDir(): void
    {
        $repo = new FilesystemFTPRepository($this->filesystem);

        /** @var MockObject $fileMock */
        $fileMock = $this->createMock(FileInterface::class);
        $fileMock->expects($this->once())->method('isDir')->willReturn(true);

        /** @var FileInterface $fileMock */
        $this->assertFalse($repo->update($fileMock, null));
    }

    public function testUpdateFile(): void
    {
        /** @var MockObject $fl */
        $fl = $this->filesystem;
        $fl->expects($this->once())->method("write")
            ->with("/folder/file.txt", "new content new content");

        $repo = new FilesystemFTPRepository($this->filesystem);

        /** @var MockObject $fileMock */
        $fileMock = $this->createMock(FileInterface::class);
        $fileMock->expects($this->exactly(2))->method('isDir')->willReturn(false);
        $fileMock->expects($this->once())
            ->method('getUniqueName')
            ->willReturn("/folder/file.txt");

        /** @var FileInterface $fileMock */
        $this->assertTrue($repo->update($fileMock, "new content new content"));
    }
}
