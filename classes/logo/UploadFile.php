<?php namespace Cleanse\League\Classes\Logo;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Storage;

class UploadFile extends \SplFileInfo
{
    public function move($directory, $name = null)
    {
        $target = $this->getTargetFile($directory, $name);

        if (!@move_uploaded_file($this->getPathname(), $target)) {
            $error = error_get_last();
            throw new FileException(
                sprintf('Could not move the file "%s" to "%s" (%s)',
                    $this->getPathname(), $target, strip_tags($error['message'])
            ));
        }

        @chmod($target, 0666 & ~umask());

        return $target;
    }

    protected function getTargetFile($directory, $name = null)
    {
        if (!is_dir($directory)) {
            if (false === @mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new Exception(sprintf('Unable to create the "%s" directory', $directory));
            }
        } elseif (!is_writable($directory)) {
            throw new Exception(sprintf('Unable to write in the "%s" directory', $directory));
        }

        $target = rtrim($directory,
                '/\\') . DIRECTORY_SEPARATOR . (null === $name ? $this->getBasename() : $this->getName($name));

        return new self($target);
    }

    protected function getName($name)
    {
        $originalName = str_replace('\\', '/', $name);
        $pos = strrpos($originalName, '/');
        $originalName = false === $pos ? $originalName : substr($originalName, $pos + 1);

        return $originalName;
    }

    protected function copyLocalToStorage($localPath, $storagePath)
    {
        return Storage::put($storagePath, FileHelper::get($localPath), ($this->isPublic()) ? 'public' : null);
    }

    /**
     * Define the internal storage path, override this method to define.
     */
    public function getStorageDirectory()
    {
        if ($this->isPublic()) {
            return 'uploads/public/';
        }
        else {
            return 'uploads/protected/';
        }
    }

    /**
     * If working with local storage, determine the absolute local path.
     * @return string
     */
    protected function getLocalRootPath()
    {
        return storage_path().'/app';
    }

    /**
     * Define the internal working path, override this method to define.
     */
    public function getTempPath()
    {
        $path = temp_path() . '/uploads';

        if (!FileHelper::isDirectory($path)) {
            FileHelper::makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
