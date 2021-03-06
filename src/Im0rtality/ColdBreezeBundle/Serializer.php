<?php

namespace Im0rtality\ColdBreezeBundle;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Serializer
{
    /** @var array */
    protected $expands = [];
    /** @var array */
    protected $mapping = [];
    /** @var array|null */
    protected $fields;
    /** @var  string[] */
    protected $ignore;

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
     * @param $object
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
        $class = current(array_reverse(explode('\\', get_class($object))));
        if (null === $this->ignore) {
            $this->ignore = @$this->mapping[$class]['ignore'] ? : [];
        }

        $fields = $this->mapping[$class]['fields'];
        $expand = $this->mapping[$class]['expand'];

        $expand += $this->expands;
        $fields = array_diff($fields, $this->ignore);

        $output = array_flip($fields);
        foreach ($fields as $field) {
            $getter = sprintf('get%s', ucfirst($field));
            if (!is_callable([$object, $getter])) {
                $getter = sprintf('is%s', ucfirst($field));
            }
            $value = $object->{$getter}();
            if ($value instanceof \DateTime) {
                $output[$field] = $value->format('c');
            } elseif ((is_array($value)) || ($value instanceof Collection)) {
                if (in_array($field, $expand)) {
                    $output[$field] = $this->serializeCollection($value);
                } else {
                    if (!($value instanceof Collection)) {
                        $value = new ArrayCollection($value);
                    }
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
