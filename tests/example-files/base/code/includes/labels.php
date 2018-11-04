<?hh //strict

namespace codeneric\phmm\base\includes;

enum InternalLabelID : string {
  Favorites = "1111111111111";
}

type LabelShape = shape(
  "id" => string,
  "name" => string,
);

class Labels {
  const allLabelsOptionName = "codeneric/phmm/labels/all";

  /**
   * Returns 13-char long uid
   */
  private static function generate_label_id(): string {
    return uniqid();
  }

  /**
   * Parses the three parameter to a MD5 hash. We use md5 non-cryptographically to get a static fixed-length output for input, in order to guarantee that we do not exceed the option_name string length limit of 256bit.
   * @param $clientID - The client id
   * @param $projectID - The project id
   * @return The option name, a prefixed hash (54 chars long)
   */
  private static function get_option_name(
    int $clientID,
    int $projectID,
    string $labelID,
  ): string {
    invariant(is_int($clientID),'%s', new Error("clientID must be int"));
    invariant(is_int($projectID),'%s', new Error("projectID must be int"));
    invariant(is_string($labelID),'%s', new Error("labelID must be string"));
    invariant($labelID !== "",'%s', new Error("labelID cannot be empty string"));

    $hash = md5("$clientID/$projectID/$labelID");

    return "codeneric/phmm/labels/$hash";
  }

  /**
   * Gets all labels
   * @return Array of labels. Label entry is an array with shape  ("id" => string, "")
   */
  public static function get_all_labels(): array<LabelShape> {

    $labels = get_option(self::allLabelsOptionName, array());
    invariant(is_array($labels),'%s', new Error('Expected labels to be of type array.'));
    return $labels;
  }
  /**
   * Checks if a label exists by id
   */
  public static function label_exists(string $id): bool {
    $labels = self::get_all_labels();

    $filter = function($label) use ($id) {
      invariant(is_array($label),'%s', new Error('label expected to be of type array'));

      invariant(
        array_key_exists("id", $label),
        '%s', new Error("key id expected to exist in label array")
      );

      return $label['id'] === $id;
    };

    $matches = array_values(array_filter($labels, $filter));

    return count($matches) === 1;

  }
  /**
   * Saves all labels
   */
  private static function save_all_labels(array<LabelShape> $labels): bool {
    return update_option(Labels::allLabelsOptionName, $labels);
  }

  /**
   * Tries to find a label entry by its name
   * @param $labelName - The label name
   */
  public static function get_label_by_name(
    string $labelName,
  ): array<LabelShape> {
    $labels = self::get_all_labels();

    $filter = function($label) use ($labelName) {
      invariant(is_array($label),'%s', new Error('label expected to be of type array'));

      invariant(
        array_key_exists("name", $label),
        '%s', new Error("key name expected to exist in label array")
      );

      return $label['name'] === $labelName;
    };

    $matches = array_values(array_filter($labels, $filter));

    //TODO: should we expect $matches length to be === 1?
    return $matches;
  }
  /**
   * Tries to find a label entry by its name
   * @param $labelName - The label name
   * @return returns the ID or null when no labels found
   */
  public static function get_label_id_by_name(string $labelName): ?string {
    $labels = self::get_label_by_name($labelName);

    invariant(
      count($labels) <= 1,
      '%s', new Error("Cannot get ID when label name matches multiple entries")
    );

    if (count($labels) === 0)
      return null;

    $label = $labels[0];

    // invariant(
    //   array_key_exists("id", $label),
    //   "key id expected to exist in label array",
    // );

    return $label['id'];

  }

  /**
   * Creates or updates a label. When $id is not given, creates a label with $name and generated id. Else update the name for given id.
   * @param $name - The label name
   * @param $id - Label id, if given. When null, new label will be created
   * @return False if labels were not updated and true if labels were updated.
   */
  public static function update_label(string $name, ?string $id): bool {
    invariant($name !== "",'%s', new Error('Label name cannot be empty string'));
    invariant($id !== "",'%s', new Error('Label id cannot be empty string'));

    $labels = self::get_all_labels();

    if (is_null($id)) {
      $copy = $labels; // PHP copies by default;
      $id = self::generate_label_id();

      array_push($copy, shape("id" => $id, "name" => $name));

      return self::save_all_labels($copy);
    }

    // else when id defined, update the name

    $map = function($label) use ($name, $id) {

      if ($label['id'] !== $id)
        return $label;

      // update the name field
      $label['name'] = $name;

      return $label;
    };

    if (count($labels) > 0) {
      $newLabels = array_map($map, $labels);
    } else {
      $newLabels = array(shape("id" => $id, "name" => $name));
    }

    // var_dump($newLabels);
    return self::save_all_labels($newLabels);

  }

  /**
   * Get an array of imageIDs for given client+project+label  combination
   * @param $clientID - The client id
   * @param $projectID - The project id
   * @param $labelID - The label id
   */
  public static function get_set(
    int $clientID,
    int $projectID,
    string $labelID,
  ): array<int> {

    $optionName = self::get_option_name($clientID, $projectID, $labelID);

    $labels = get_option($optionName, array());

    invariant(is_array($labels),'%s', new Error('Expected labels to be of type array.'));

    return $labels;
  }

  /**
   * Save set of imageIDs for given client + project + label combination
   * @param $clientID - The client id
   * @param $projectID - The project id
   * @param $labelID - The label id
   * @param $imageIDs - The set of image IDs that should be saved as favorites
   */
  public static function save_set(
    int $clientID,
    int $projectID,
    string $labelID,
    array<int> $imageIDs,
  ): bool {

    invariant(is_array($imageIDs),'%s', new Error('Expected labels to be of type array.'));

    // check if labelID exists
    $optionName = self::get_option_name($clientID, $projectID, $labelID);

    return update_option($optionName, $imageIDs);
  }
  /**
   * Delete set of imageIDs for given client + project + label combination
   * @param $clientID - The client id
   * @param $projectID - The project id
   * @return Bool whether the deletion was successful. False can mean either failure, or option was not existent in the first place.
   */
  public static function delete_set(
    int $clientID,
    int $projectID,
    string $labelID,
  ): bool {
    $optionName = self::get_option_name($clientID, $projectID, $labelID);

    return delete_option($optionName);
  }

  /**
   * check for label and delete it, except for predefined IDs
   * @param $string - label to delete
   * @param Bool true if deletion was successful. False can mean either failure, or option was not existent in the first place.
   */
  public static function delete_label(string $id): bool {
    // get all labels
    $labels = self::get_all_labels();

    // make sure $labels is an array
    invariant(is_array($labels),'%s', new Error('get all labels does not return array!'));
    invariant((count($labels) !== 0),'%s', new Error('_labels is an empty array'));
    invariant(
      !InternalLabelID::isValid($id),
      '%s', new Error('internal labels cannot be deleted')
    );

    foreach ($labels as $index => $label) {
      invariant(
        array_key_exists('id', $label),
        '%s', new Error('_label does not have an id!')
      );
      if ($label['id'] == $id) {
        // drop label from labels
        unset($labels[$index]);
        // delete all sets which contain label id
        $client_ids = Client::get_all_ids();
        invariant(is_array($client_ids),'%s', new Error('client_ids is not an array'));
        $project_ids = Project::get_all_ids();
        invariant(is_array($project_ids),'%s', new Error('project_ids is not an array'));

        // TODO: may be slow with lots of entries
        if (count($client_ids) !== 0 && count($project_ids) !== 0) {
          foreach ($client_ids as $client_id) {
            foreach ($project_ids as $project_id) {
              self::delete_set($client_id, $project_id, $id);
            }
          }
        }
      }
    }

    // update label option
    return self::save_all_labels($labels);
  }
  /**
   * hook for admin_hooks
   * init, make sure that all enum IDs (labels) are present
   */
  public static function initLabelStore(): void {

    $res = self::get_all_labels();
    invariant(is_array($res),'%s', new Error('result of get all labels is not an array!'));
    if (count($res) != 0) {
      foreach ($res as $label) {
        invariant(
          array_key_exists('id', $label),
          '%s', new Error('key does not exist in _label')
        );
        if (!InternalLabelID::isValid($label['id'])) {
          invariant(is_string($label['id']),'%s', new Error('_label id is not a string'));
          invariant(is_string($label['name']),'%s', new Error('_label name is not a string'));
          // add label here
          $bool = self::update_label($label['name'], $label['id']);
          if ($bool) {
            // success
          } else {
            // TODO ^ couldnt update labels
          }
        }
      }
    } else {
      foreach (InternalLabelID::getValues() as $key => $val) {
        invariant(is_string($key),'%s', new Error('_key is not a string'));
        invariant(is_string($val),'%s', new Error('_val name is not a string'));
        $bool = self::update_label($key, $val);
        if ($bool) {
          // success
        } else {
          // TODO ^ couldnt update labels
        }
      }
    }
  }

}
