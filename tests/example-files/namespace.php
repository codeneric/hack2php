<?hh //strict


namespace codeneric\blub {
  type b = shape();

}
namespace codeneric\x {
  use \codeneric\blub;
  function f(blub\b $a):void{}
}
