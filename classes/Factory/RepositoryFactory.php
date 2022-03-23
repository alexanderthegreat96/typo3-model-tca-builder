<?php
namespace LexSystems\Core\System\Factories;
use LexSystems\Core\System\Factories\AbstractModelFactory;

class RepositoryFactory extends AbstractModelFactory
{
    /**
     * @param string $className
     * @param string $tablename
     * @param array $columns
     * @return string
     */
    private function writeClass(string $className = '')
    {
        $pluginName = 'tx_'.$this->convertExtKeyToPluginName($this->extKey);

        $template = '<?php ';
        $template .= '
declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace '.$this->generateNamespace().'\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
/**
 * '.$className.' repository
 */
class '.$className.' extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * @var int
     */
     
     protected $currentPid;
    
    /**
     * Default ordering for all queries created by this repository
     */
    protected $defaultOrderings = array(
        "crdate" => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
    );
    
    /**
     * sets storage pid for given plugin
     * based on typoscript
     * do not forget to add -> tx_pluginnamehere{settings{storagePid = 123}}
     */
     
    protected function getStoragePid()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get(ConfigurationManager::class);
        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        if ($extbaseFrameworkConfiguration["plugin."]["'.$pluginName.'."]["settings."]["storagePid"]) {
            $this->currentPid = $extbaseFrameworkConfiguration["plugin."]["'.$pluginName.'."]["settings."]["storagePid"];
        } else {
            $this->currentPid = 1;
        }

        return $this->currentPid;
    }
    
    /**
     * Initialize repository settings
     */
     
    public function initializeObject()
    {
        /** @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */ 
        $querySettings = $this->objectManager->get("TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettingsInterface");
        $querySettings->setRespectStoragePage(true);
        $querySettings->setStoragePageIds([$this->getStoragePid()]);
        $this->setDefaultQuerySettings($querySettings);
    }
    
}';


        if(!file_exists(__DIR__ . '/../../Generated/Repositories/' .$className.'.php'))
        {
            $try = file_put_contents(__DIR__ . '/../../Generated/Repositories/' .$className.'.php',$template);
            if($try)
            {
                return $className . " has been created in Repositories \n";
            }
            else
            {
                return $className . " could not be written, permission issues are most likely the cause!\n";
            }
        }
        else
        {
            return $className . " skipped, as it already exists.!\n";
        }
    }

    /**
     * @param array $tableNames
     * @return array|void
     */
    public function build(array $tableNames = [])
    {
        if($tableNames)
        {
            $i = 1;
            foreach ($tableNames as $tableName)
            {
                $className = $this->generateClassNameFromString($tableName).'Repository';
                $this->writeClass($className);
                $repos[] = $className;
            }

            return $repos;

        }
        else
        {
            print("No tables provided\n");
        }
    }
}