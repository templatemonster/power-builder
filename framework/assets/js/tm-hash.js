var tm_hash_module_seperator = '||',
  tm_hash_module_param_seperator = '|';

function process_tm_hashchange( hash ) {
  if ( ( hash.indexOf( tm_hash_module_seperator, 0 ) ) !== -1 ) {
    modules = hash.split( tm_hash_module_seperator );
    for ( var i = 0; i < modules.length; i++ ) {
      var module_params = modules[i].split( tm_hash_module_param_seperator );
      var element = module_params[0];
      module_params.shift();
      if ( $('#' + element ).length ) {
        $('#' + element ).trigger({
          type: "tm_hashchange",
          params: module_params
        });
      }
    }
  } else {
    module_params = hash.split( tm_hash_module_param_seperator );
    var element = module_params[0];
    module_params.shift();
    if ( $('#' + element ).length ) {
      $('#' + element ).trigger({
        type: "tm_hashchange",
        params: module_params
      });
    }
  }
}

function tm_set_hash( module_state_hash ) {
  module_id = module_state_hash.split( tm_hash_module_param_seperator )[0];
  if ( !$('#' + module_id ).length ) {
    return;
  }

  if ( window.location.hash ) {
    var hash = window.location.hash.substring(1), //Puts hash in variable, and removes the # character
      new_hash = [];

    if ( ( hash.indexOf( tm_hash_module_seperator, 0 ) ) !== -1 ) {
      modules = hash.split( tm_hash_module_seperator );
      var in_hash = false;
      for ( var i = 0; i < modules.length; i++ ) {
        var element = modules[i].split( tm_hash_module_param_seperator )[0];
        if ( element === module_id ) {
          new_hash.push( module_state_hash );
          in_hash = true;
        } else {
          new_hash.push( modules[i] );
        }
      }
      if ( !in_hash ) {
        new_hash.push( module_state_hash );
      }
    } else {
      module_params = hash.split( tm_hash_module_param_seperator );
      var element = module_params[0];
      if ( element !== module_id ) {
        new_hash.push( hash );
      }
      new_hash.push( module_state_hash );
    }

    hash = new_hash.join( tm_hash_module_seperator );
  } else {
    hash = module_state_hash;
  }

  var yScroll = document.body.scrollTop;
  window.location.hash = hash;
  document.body.scrollTop = yScroll;
}
