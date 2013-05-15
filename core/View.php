<?php
/**
 * @author: Sergey Tihonov
 */

class View
{
    /**
     * @var string
     */
    protected $_currentTemplate;

    /**
     * @var array
     */
    protected $_variables = [];

    /**
     * @var string
     */
    protected $_templateDir;

    /**
     * @param string $templateDir
     */
    public function __construct($templateDir)
    {
        if (!is_readable($templateDir)) {
            throw new InvalidArgumentException('Template dir is not readable or not found.');
        }

        $this->_templateDir = $templateDir;
    }

    /**
     * @param string $templateName
     * @return View
     * @throws InvalidArgumentException
     */
    public function template($templateName)
    {
        $realTemplateFile = sprintf('%s/%s.phtml', $this->_templateDir, $templateName);
        if (!is_readable($realTemplateFile)) {
            throw new InvalidArgumentException('Template file is not readable or not found.');
        }
        $this->_currentTemplate = $realTemplateFile;
        return $this;
    }

    /**
     * @param string $varname
     * @param mixed $value
     */
    protected function _setVar($varname, $value)
    {
        $this->_variables[$varname] = $value;
    }

    /**
     * @param string $varname
     * @param mixed $value
     */
    public function __set($varname, $value)
    {
        $this->_setVar($varname, $value);
    }

    /**
     * @return string
     */
    public function render()
    {
        ob_start();
        extract($this->_variables);
        include $this->_currentTemplate;
        $result = ob_get_contents(); ob_end_clean();
        return $result;
    }

    /**
     * @param      $services
     * @param      $pid
     * @param      $lvl
     * @param null $parentId
     */
    public function helperServicesSelectList($services, $pid, $lvl, $parentId = null)
    {
        foreach($services as $service) {
            if($service['parent_id'] == $pid) {
                $s = "";
                for($k = 0; $k < $lvl; $k++) $s .= " - ";
                echo '<option value="'. $service['id'] .'"';
                if (!is_null($parentId) && $service['id'] == $parentId) {
                    echo ' selected ';
                }
                echo '>'. $s.$service['name'] . '</options>';
                $this->helperServicesSelectList($services, $service['id'], $lvl + 1, $parentId);
            }
        }
    }

    /**
     * @param $services
     * @param $pid
     * @param $lvl
     */
    public function helperServicesList($services, $pid, $lvl)
    {
        foreach($services as $service) {
            if($service['parent_id'] == $pid) {
                echo '<ul ' , ($lvl > 0) ? 'class="child-service"' : '' , '>';
                echo '<li id="service' , $service['id'] , '"><p>' , $service['name'];
                echo '<span class="controll"><a href="#" onclick="serviceEdit(this);return false;">edit</a><a href="#"  onclick="serviceDel(this);return false;">del</a></span>';
                echo '</p></li>';
                $this->helperServicesList($services, $service['id'], $lvl + 1);
                echo '</ul>';
            }
        }
    }
}