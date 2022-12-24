<?php

declare(strict_types=1);

namespace Supermetrolog\SynchronizerFilesystemFTPTargetRepo;

use Supermetrolog\Synchronizer\interfaces\FileInterface;
use Supermetrolog\Synchronizer\interfaces\TargetRepositoryInterface;
use Supermetrolog\SynchronizerAlreadySyncRepo\interfaces\RepositoryInterface;

class FilesystemFTPRepository implements TargetRepositoryInterface, RepositoryInterface
{
    private FTPFilesystem $filesystem;

    public function __construct(FTPFilesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function remove(FileInterface $file): bool
    {
        try {
            if ($file->isDir()) {
                $this->filesystem->deleteDirectory($file->getUniqueName());
            } else {
                $this->filesystem->delete($file->getUniqueName());
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function create(FileInterface $file, ?string $content): bool
    {
        try {
            if ($file->isDir()) {
                $this->filesystem->createDirectory($file->getUniqueName());
            } else {
                $this->filesystem->write($file->getUniqueName(), $content ?? "");
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function update(FileInterface $file, ?string $content): bool
    {
        try {
            if ($file->isDir()) {
                return false;
            } else {
                return $this->create($file, $content);
            }
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function fileExist(FileInterface $file): bool
    {
        if ($file->isDir()) {
            return $this->filesystem->directoryExists($file->getUniqueName());
        }
        return $this->filesystem->fileExists($file->getUniqueName());
    }

    public function createOrUpdate(string $filename, string $content): bool
    {
        try {
            $this->filesystem->write($filename, $content);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function getContentByFilename(string $filename): ?string
    {
        if (!$this->filesystem->fileExists($filename)) {
            return null;
        }
        return $this->filesystem->read($filename);
    }
}
