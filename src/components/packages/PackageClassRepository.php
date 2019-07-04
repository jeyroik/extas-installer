<?php
namespace extas\components\packages;

use extas\components\repositories\Repository;
use extas\interfaces\IItem;
use extas\interfaces\packages\IPackageClass;
use extas\interfaces\repositories\IRepository;

/**
 * Class PackageClassRepository
 *
 * @package extas\components\packages
 * @author jeyroik@gmail.com
 */
class PackageClassRepository extends Repository implements IRepository
{
    protected $itemClass = PackageClass::class;
    protected $scope = 'extas';
    protected $pk = PackageClass::FIELD__INTERFACE_NAME;
    protected $name = 'packages_classes';
    protected $data = [];
    protected $dsn = '/configs/container.json';
    protected $dsnLock = '/configs/container.php';

    /**
     * CompareRepository constructor.
     *
     * @param $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $envDsn = getenv('EXTAS__' . strtoupper($this->getName())) ?: '';
        $envDsnLock = getenv('EXTAS__' . strtoupper($this->getName()) . '_LOCK') ?: '';

        $this->dsn = $envDsn ?: getenv('EXTAS__BASE_PATH') . $this->dsn;
        $this->dsnLock = $envDsnLock ?: getenv('EXTAS__BASE_PATH') . $this->dsnLock;

        if (is_file($this->dsn)) {
            $this->data = json_decode(file_get_contents($this->dsn), true);
        }
    }

    /**
     * @param $where
     *
     * @return mixed|null
     */
    public function one($where)
    {
        foreach ($this->data as $item) {
            $equal = true;
            foreach ($where as $field => $value) {
                if (!isset($item[$field])) {
                    continue 2;
                }

                if ($item[$field] != $value) {
                    $equal = false;
                    break;
                }
            }
            if ($equal) {
                $itemClass = $this->itemClass;
                return new $itemClass($item);
            }
        }

        return null;
    }

    /**
     * @param $where
     *
     * @return array
     */
    public function all($where)
    {
        $items = [];

        foreach ($this->data as $item) {
            $equal = true;
            foreach ($where as $field => $value) {
                if (!isset($item[$field])) {
                    continue 2;
                }

                if ($item[$field] != $value) {
                    $equal = false;
                    break;
                }
            }
            if ($equal) {
                $itemClass = $this->itemClass;
                $items[] = new $itemClass($item);
            }
        }

        return $items;
    }

    /**
     * @param $item IItem|mixed
     *
     * @return bool|mixed
     */
    public function create($item)
    {
        $this->data[] = $item instanceof IItem ? $item->__toArray() : (array) $item;
        $this->commit();

        return true;
    }

    /**
     * @param $item IItem|mixed
     * @param $where
     *
     * @return int
     */
    public function update($item, $where = []): int
    {
        $uid = $this->getItemUID($item);
        $byUid = $this->getDataByUID();

        if (isset($byUid[$uid])) {
            $byUid[$uid] = $item->__toArray();
            $this->data = array_values($byUid);
            $this->commit();
            return 1;
        }

        return 0;
    }

    /**
     * @param $where
     * @param $item IItem|mixed
     *
     * @return int
     */
    public function delete($where, $item = null): int
    {
        $byUID = $this->getDataByUID();
        $uid = $this->getItemUID($item);

        if (isset($byUID[$uid])) {
            unset($byUID[$uid]);
            $this->data = array_values($byUID);
            $this->commit();
            return 1;
        }

        return 0;
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        file_put_contents($this->dsn, json_encode($this->data));
        return true;
    }

    /**
     *
     */
    public function createLockFile()
    {
        $result = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

        foreach ($this->data as $index => $item) {
            $result .= $item[IPackageClass::FIELD__INTERFACE_NAME] . '::class'
                . ' => '
                . $item[IPackageClass::FIELD__CLASS_NAME] . '::class';

            if (isset($this->data[$index+1])) {
                $result .= ',' . PHP_EOL;
            }
        }

        $result .= '];' . PHP_EOL;

        /**
         * Result example:
         *
         * <?php
         * return [
         * <Interface>::class => <Class>::class
         * ];
         */
        file_put_contents($this->dsnLock, $result);
    }

    /**
     * @param $item IItem|mixed
     *
     * @return string
     */
    protected function getItemUID($item)
    {
        $getUid = 'get' . ucfirst($this->pk);
        if (method_exists($item, $getUid)) {
            return $item->$getUid();
        } else {
            return '';
        }
    }

    /**
     * @return array
     */
    protected function getDataByUID()
    {
        return array_column($this->data, null, $this->pk);
    }
}
