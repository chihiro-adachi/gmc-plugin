<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\GMC;


use Eccube\Common\EccubeConfig;
use Eccube\Plugin\AbstractPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Trikoder\Bundle\OAuth2Bundle\Manager\ClientManagerInterface;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;
use Trikoder\Bundle\OAuth2Bundle\Model\Grant;
use Trikoder\Bundle\OAuth2Bundle\Model\RedirectUri;
use Trikoder\Bundle\OAuth2Bundle\Model\Scope;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Grants;

class PluginManager extends AbstractPluginManager
{
    public function install(array $meta, ContainerInterface $container)
    {
        /** @var ClientManagerInterface $clientManager */
        $clientManager = $container->get(ClientManagerInterface::class);
        $Client = new Client('eccube_gmc_client', hash('sha512', random_bytes(32)));
        $Client->setScopes(
            new Scope('read'),
            new Scope('write'));
        $Client->setGrants(
            new Grant(OAuth2Grants::AUTHORIZATION_CODE),
            new Grant(OAuth2Grants::REFRESH_TOKEN));
        $Client->setRedirectUris(new RedirectUri(env('GMC_PROXY_URL', 'https://gmc-proxy.ec-cube.net').'/eccube/callback'));
        $clientManager->save($Client);

        /** @var EccubeConfig $eccubeConfig */
        $eccubeConfig = $container->get(EccubeConfig::class);
        @mkdir($eccubeConfig->get('plugin_data_realdir') . '/GMC');
    }
}