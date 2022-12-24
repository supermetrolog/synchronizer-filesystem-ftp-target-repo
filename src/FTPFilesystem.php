<?php

declare(strict_types=1);

namespace Supermetrolog\SynchronizerFilesystemFTPTargetRepo;

use League\Flysystem\Config;
use League\Flysystem\Ftp\FtpAdapter;

class FTPFilesystem
{
    private FtpAdapter $ftp;

    /** @var \FTP\Connection|resource $connection */
    private $connection;

    /** @param \FTP\Connection|resource $connection */
    public function __construct(FtpAdapter $ftp, $connection)
    {
        $this->ftp = $ftp;
        $this->connection = $connection;
    }

    public function directoryExists(string $path)
    {
        $prevDir = ftp_pwd($this->connection);
        $result = ftp_chdir($this->connection, $path);
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
}
