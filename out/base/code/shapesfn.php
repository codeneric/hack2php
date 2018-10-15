<?php
/*
 This is a utility fake class
 In hacklang there exists a Shapes class in runtime to do actions on shape types.
 In our hack->php transpiler, this class does not exist. However, the hack type system knows of the existence of the class.
 Hence, we here mock the class so we do not get runtime errors and take advantage of the type system.
 Since shapes are arrays under the hood, we treat them here as such.
 */
class Shapes {
  static public function toArray($shape) {
    return $shape;
  }
}
