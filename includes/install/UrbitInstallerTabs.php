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
 * Class UrbitInstallerTabs
 */
class UrbitInstallerTabs extends UrbitInstallerEntity
{
    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function install()
    {
        $flag = true;

        $TAB_CLASS = UrbitAbstract::CLASS_PARENT_TAB;

        if ($TAB_CLASS) {
            $id_tab = (int) Tab::getIdFromClassName($TAB_CLASS);

            $flag = $this->installModuleTab(
                $this->module->class_controller_admin,
                $this->module->displayName,
                $id_tab
            );
        }

        return $flag;
    }

    /**
     * Install an Admin Tab (menu)
     * @param string $tab_class
     * @param string $tab_name
     * @param int $id_tab_parent
     * @param int $position
     * @return boolean
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function installModuleTab($tab_class, $tab_name, $id_tab_parent = -1, $position = 0)
    {
        $tab = new Tab();
        $name = array();

        foreach (Language::getLanguages(false) as $language) {
            $name[$language['id_lang']] = $tab_name;
        }

        $tab->name = $name;
        $tab->class_name = (string)$tab_class;
        $tab->module = $this->module->name;

        if ($id_tab_parent != null) {
            $tab->id_parent = (int)$id_tab_parent;
        }

        if ((int) $position > 0) {
            $tab->position = (int) $position;
        }

        return $tab->add(true);
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function uninstall()
    {
        $id_tab = (int) Tab::getIdFromClassName((string) $this->module->class_controller_admin);

        if ($id_tab != 0) {
            $tab = new Tab($id_tab);

            if (Validate::isLoadedObject($tab)) {
                return $tab->delete();
            }
        }
        return true;
    }
}
