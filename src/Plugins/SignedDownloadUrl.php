<?php
/**
 * @category SignedDownloadUrl
 * @created 2020/8/11 10:23
 * @since
 */

namespace Otter\AliOss\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

class SignedDownloadUrl extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'signedDownloadUrl';
    }

    /**
     * Handle.
     *
     * @param string $path
     * @param int    $expires
     * @param string $host_name
     * @param bool   $use_ssl
     * @return string|false
     */
    public function handle($path, $expires = 3600, $host_name = '', $use_ssl = false)
    {
        if (! method_exists($this->filesystem, 'getAdapter')) {
            return false;
        }

        if (! method_exists($this->filesystem->getAdapter(), 'getSignedDownloadUrl')) {
            return false;
        }

        return $this->filesystem->getAdapter()->getSignedDownloadUrl($path, $expires, $host_name, $use_ssl);
    }
}
