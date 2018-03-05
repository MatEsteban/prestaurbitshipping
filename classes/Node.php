<?php
/**
 * Node of Urb-it module
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license Urb-it
 */

class Node
{
    /**
     * @var string $name the node name
     */
    protected $name = null;

    /**
     * @var $slug a frienly url format
     */
    protected $slug = null;

    /**
     * @var Node $parent a reference to the parent node
     */
    protected $parent = null;

    /**
     * @var array $children an integer indexed array containing all the references to the child nodes
     */
    protected $children = null;

    /**
     * @var int $count the number of child nodes
     */
    protected $count = null;
    protected $link = null;
    protected $addClass = null;

    /**
     * Constructor
     * @param string $name the node name
     */
    public function __construct($name = null, $link = null, $slug = null, $addClass = null)
    {
        $this->name = (string)$name;  // ensure the $name is a string an assign it to the node name
        $this->slug = empty($slug) ? $this->generateSlug($this->name) : $slug;
        $this->link = empty($link) ? null : $link;
        $this->addClass = empty($addClass) ? null : $addClass;
        $this->children = array();    // initialise the childs array
        $this->count = 0;  // initialise the count of the childs array
    }

    /**
     * @return string slug
     */
    protected function generateSlug($string)
    {
        return Tools::strtolower(preg_replace('![^a-z0-9]+!i', '-', $string));
    }

    /**
     * @return string $name the node name
     */
    public function getName()
    {
        return $this->name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getClass()
    {
        return $this->addClass;
    }

    /**
     * @return Node $parent the reference to the parent node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return array $children the integer indexed array containing all the references to the child nodes
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return int $count the number of child nodes
     */
    public function getChildrenCount()
    {
        return $this->count;
    }

    /**
     * @return bool wether : true if there is no child contained in the children array, else false
     */
    public function isEmpty()
    {
        return ($this->count == 0);
    }

    /**
     * @return the xml output of the node
     */
    public function __toString()
    {
        return $this->export();
    }

    /**
     * export the node and all his childs to an xml format
     * @param $level the node indentation deep
     * @return string $x the xml output of the node
     */
    public function export($level = null)
    {
        $level = (int)$level;
        // if it's the root level element
        $ws = str_repeat('  ', $level);  // calculate the whitespace, TO DO : this one could be a constant
        if ($this->count == 0) {
            return "$ws<$this->name/>\n";
        }  // return a closing tag of the current node name as the xml output

        $x = "$ws<$this->name>\n";    // open an tag of the current node name
        foreach ($this->children as $child) {
            $x .= $child->export($level + 1);
        }    // export the child node
        $x .= "$ws</$this->name>\n";  // close the opened tag of the current node name
        return $x;  // return the xml output of the current node
    }

    /**
     * import all xml nodes from a SimpleXMLElement
     * @param SimpleXMLElement $sxe the xml fragment of the last import method iteration
     * @param Node $p the parent node reference of the last import method iteration
     */
    public function import(SimpleXMLElement $sxe, Node $p = null)
    {
        $this->name = $sxe->getName();   // reasign the node name with the root element of the SimpleXMLElelment
        $this->parent = $p;    // assign the parent node
        if (count($sxe->children()) > 0) {
            // if the xml node is not empty
            foreach ($sxe->children() as $child) {
                // for each children
                $n = new Node($child->getName());   // create the new node
                $this->addChild($n);  // append the new node
                $n->import($child, $this);   // now import all nodes from the child element of the SimpleXMLElement
            }
        }
    }

    /**
     * Wether :
     * - create a public user defined property called by the node $n name and
     *   assign the node $n to this user defined property
     * - append the node $n to the public user defined property called by the node $n name
     * @param Node $n the node to append
     * @return Node
     */
    public function addChild(Node $n)
    {
        $n->parent = $this;    // assign the parent node to the node $n
        $name = $n->name;  // get the node name
        // check wether the public user defined property $name is already defined
        if (!(isset($this->$name))) {
            $this->$name = $n;
        } else {
            if (!(is_array($this->$name))) {
                $this->$name = array($this->$name);
            } // convert the user defined property as an array
            array_push($this->$name, $n);  // append the new node $n to the user defined property
        }
        array_push($this->children, $n); // append the new node $n to the childs array
        $this->count++; // increments $this->count the number of child nodes
        return $n;
    }
}
