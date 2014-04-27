<?php

namespace Im0rtality\ColdBreezeBundle;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\User;

class Serializer
{
    /** @var array */
    protected $expands = [];
    /** @var array */
    protected $mapping = [];
    /** @var array|null */
    protected $fields;

    /**
     * @param array $expands
     */
    public function setExpands($expands)
    {
        $this->expands = $expands;
    }

    /**
     * @param array $mapping
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param User $object
     * @return array
     */
    public function serialize($object)
    {
        if ((is_array($object)) || ($object instanceof Collection)) {
            return $this->serializeCollection($object);
        } else {
            return $this->serializeInstance($object);
        }

    }

    /**
     * @param Collection $collection
     * @return array
     */
    private function serializeCollection($collection)
    {
        $output = [];
        foreach ($collection as $item) {
            $output[] = $this->serializeInstance($item);
        }

        return $output;
    }

    /**
     * @param $object
     * @return array
     */
    private function serializeInstance($object)
    {
        $fields = $this->mapping[current(array_reverse(explode('\\', get_class($object))))]['fields'];
        $expand = $this->mapping[current(array_reverse(explode('\\', get_class($object))))]['expand'];

        $expand += $this->expands;

        $output = array_flip($fields);
        foreach ($fields as $field) {
            $getter = sprintf('get%s', ucfirst($field));
            $value  = $object->{$getter}();
            if ($value instanceof \DateTime) {
                $output[$field] = $value->format('c');
            } elseif ($value instanceof Collection) {
                if (in_array($field, $expand)) {
                    $output[$field] = $this->serializeCollection($value);
                } else {
                    $output[$field] = $value
                        ->map(
                            function ($item) {
                                return $item->getId();
                            }
                        )
                        ->toArray();
                }
            } elseif (is_object($value)) {
                if (in_array($field, $expand)) {
                    $output[$field] = $this->serialize($value);
                } else {
                    $output[$field] = $value->{'getId'}();
                }
            } else {
                $output[$field] = $value;
            }
        }

        return $output;
    }
}
