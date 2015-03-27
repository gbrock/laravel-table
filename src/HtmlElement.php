<?php namespace Gbrock\Table;

class HtmlElement {
    private $tag = '';
    private $attributes = [];
    private $classes = [];
    private $children = [];

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $tag = strtolower($tag);
        $tag = preg_replace('/[^a-z0-9\-_]+/', '', $tag);

        $this->tag = $tag;
    }

    /**
     * Set a specific attribute's value.
     * If the attribute is "class", add it to the class array instead.
     *
     * @param $attribute
     * @param null $value NULL generates a value-less attribute.
     */
    public function setAttribute($attribute, $value = NULL)
    {
        if($attribute == 'class')
        {
            $this->addClass($value);
            return;
        }

        $this->attributes[$attribute] = $value;
    }

    /**
     * Set each passed attribute.
     * Does not remove existing keys not present in the passed array.
     *
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        if(!is_array($attributes))
        {
            if($attributes !== NULL)
            {
                // Not an array or null; we can't do anything
                throw new \InvalidArgumentException;
            }

            $this->clearAttributes();
            return;
        }

        foreach($attributes as $k => $v)
        {
            $this->setAttribute($k, $v);
        }
    }

    /**
     * Remove all attributes from the element.
     */
    private function clearAttributes()
    {
        $this->attributes = NULL;
    }

    /**
     * Remove one attribute from the element.
     *
     * @param $attribute
     */
    private function removeAttribute($attribute)
    {
        if(isset($this->attributes[$attribute]))
        {
            unset($this->attributes[$attribute]);
        }
    }

    /**
     * Dynamically add a class to an element.
     *
     * @param $newClass
     */
    public function addClass($newClass)
    {
        $classes = $this->getClassArrayFromString($newClass);

        foreach($classes as $class)
        {
            if(!in_array($this->classes, $class))
            {
                // Add the class string to our array of known classes
                $this->classes[] = $class;
            }
        }
    }

    /**
     * Dynamically remove a class from an element.
     * @param $value
     */
    public function removeClass($value)
    {
        $classes = $this->getClassArrayFromString($value);

        foreach($classes as $class)
        {
            // First make sure we have this class applied
            if(in_array($class, $this->classes))
            {
                // Find the array position and unset it
                $arrayPosition = array_search($class, $this->classes);
                unset($this->classes[$arrayPosition]);
            }
        }
    }


    /**
     * Alias of addClass since they are functionally identical.
     * @param $value
     */
    public function addClasses($value) { return $this->addClass($value); }

    /**
     * Provides a common means of generating a clean, space-character-free array of class names.
     *
     * @param $value
     * @return array
     */
    private function getClassArrayFromString($value)
    {
        if(is_array($value))
        {
            // Implode back to a string for sanitation reasons
            $value = implode(' ', $value);
        }

        // Remove double spaces and return the value as a nice clean space-less array
        return array_filter(explode(' ', $value));
    }
}
