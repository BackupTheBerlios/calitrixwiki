<?PHP
/*
 * CalitrixWiki (c) Copyright 2004 by Johannes Klose
 * E-Mail: exe@calitrix.de
 * Project page: http://developer.berlios.de/projects/calitrixwiki
 * 
 * CalitrixWiki is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * CalitrixWiki is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CalitrixWiki; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **/

/**
 * This function returns a instance (and creates a new one
 * if there isnt already one) of a class.
 * 
 * @author Johannes Klose <exe@calitrix.de>
 * @param  string $class Class from where an instance should be returned.
 * @return object        Instance of $class
 **/
function &singleton($class)
{
	static $singleton;
	
	if (!is_object($singleton)) {
		$singleton = new singleton();
	}
	
	return $singleton->instance($class);
}

/**
 * Singleton class. It returns a reference to an instance and 
 * additionaly creates a new one if there isnt already one.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class singleton
{
	var $instances = array();
	
	function singleton() { }

	/**
	 * Gets an instance of a class.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string    $class Class name
	 * @return reference        Reference to an instance of $class
	 **/
	function &instance($class)
	{
		if(!isset($this->instances[$class]) || !is_object($this->instances[$class])) {
			$this->instances[$class] = new $class();
		}
		
		$ref = &$this->instances[$class];
		return $ref;
	}
}
?>