<?php

declare(strict_types=1);

namespace Supermetrolog\SynchronizerFilesystemFTPTargetRepo;

use League\Flysystem\Config;
use League\Flysystem\Ftp\FtpAdapter;
use LogicException;

class FTPFilesystem
{
    private FtpAdapter $ftp;

    /**
     * @phpstan-ignore-next-line
     * @var \FTP\Connection|resource $connection
     */
    private $connection;

    /**
     * @phpstan-ignore-next-line
     * @param \FTP\Connection|resource $connection
     */
    public function __construct(FtpAdapter $ftp, $connection)
    {
        $this->ftp = $ftp;
        $this->connection = $connection;
    }

    public function directoryExists(string $path): bool
    {
        /** @phpstan-ignore-next-line */
        $prevDir = ftp_pwd($this->connection);
        if ($prevDir === false) {
            throw new LogicException("FTP PWD command error");
        }
        /** @phpstan-ignore-next-line */
        $result = ftp_chdir($this->connection, $path);
        /** @phpstan-ignore-next-line */
        ftp_chdir($this->connection, $prevDir);
        return $result;
    }

    public function fileExists(string $path): bool
    {
        return $this->ftp->fileExists($path);
    }
    public function deleteDirectory(string $path): void
    {
        $this->ftp->deleteDirectory($path);
    }
    public function delete(string $path): void
    {
        $this->ftp->delete($path);
    }

    public function createDirectory(string $path): void
    {
        $this->ftp->createDirectory($path, new Config());
    }

    public function write(string $path, string $contents): void
    {
        $this->ftp->write($path, $contents, new Config());
    }

    public function read(string $path): string
    {
        return $this->ftp->read($path);
    }
}
