<?php
/**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license   Urb-it
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class UrbitInstaller
 */
class UrbitInstaller
{

    /**
     * @var UrbitAbstract
     */
    protected $module;

    /**
     * @var object
     */
    protected $context;

    /**
     * @var UrbitInstallerEntity[]
     */
    protected $entities = array();

    /**
     * construct
     * @param UrbitAbstract $module
     */
    public function __construct(UrbitAbstract $module)
    {
        $this->module = $module;

        $this->initContext();
        $this->loadEntities();
    }

    /**
     * Load module installation entities
     */
    protected function loadEntities()
    {
        $dir = dirname(__FILE__) . "/install";

        require_once "{$dir}/UrbitInstallerEntity.php";

        /** @var UrbitInstallerEntity $cls */
        foreach (array(
            'UrbitInstallerConfig',
            'UrbitInstallerTables',
            'UrbitInstallerCarriers',
            'UrbitInstallerWarehouseCarriers',
            'UrbitInstallerTabs',
            'UrbitInstallerHooks',
        ) as $cls) {
            require_once "{$dir}/{$cls}.php";
            $this->entities[] = new $cls($this->module, $this->context);
        }
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function install()
    {
        foreach ($this->entities as $entity) {
            if (!$entity->install()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function uninstall()
    {
        foreach ($this->entities as $entity) {
            if (!$entity->uninstall()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Init module context
     */
    protected function initContext()
    {
        if (class_exists('Context')) {
            $this->context = Context::getContext();
        } else {
            // global $smarty, $cookie;
            $smarty = $this->context->smarty;
            $cookie = $this->context->cookie;

            $this->context = (object)array(
                "smarty" => $smarty,
                "cookie" => $cookie,
            );
        }
    }
}
