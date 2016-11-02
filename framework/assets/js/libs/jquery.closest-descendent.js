(function($){
  jQuery.fn.closest_descendent = function( selector ) {
    var $found,
      $current_children = this.children();

    while ( $current_children.length ) {
      $found = $current_children.filter( selector );
      if ( $found.length ) {
        break;
      }
      $current_children = $current_children.children();
    }

    return $found;
  };
}(jQuery));
