<?hh

$func = function(
    mixed $data,
  ): (\type\plugin_settings_data_representation_3_6_5, \JsonSchema\Validator) {
    list($d, $v) =
      Handler::validate($data, "plugin_settings_data_representation_3_6_5.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  }; 