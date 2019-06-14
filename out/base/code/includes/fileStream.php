<?php //strict
namespace codeneric\phmm\base\includes;

class FileStream {

  public static function export_label_csv($data){
    // No point in creating the export file on the file-system. We'll stream
    // it straight to the browser. Much nicer.

    if (\count($data) === 0)
      exit();

    // Open the output stream
    $fh = \fopen('php://output', 'w');

    // Start output buffering (to capture stream contents)
    \ob_start();

    // CSV Header
    $header = array(
      'Label Name',
      'Client ID',
      'Client Name',
      'Original filename',
      'Wordpress File ID',
    );
    \fputcsv($fh, $header);

    // CSV Data
    foreach ($data as $k => $entry) {
      $line = array(
        $entry['label_name'],
        $entry['client_id'],
        $entry['client_name'],
        $entry['original_filename'],
        $entry['wordpress_file_id'],
      );
      \fputcsv($fh, $line);
    }

    // Get the contents of the output buffer
    $string = \ob_get_clean();

    // Set the filename of the download
    $filename =
      $data[0]['client_name'].
      '_'.
      $data[0]['label_name'].
      '_'.
      \date('Y_m_d').
      '-'.
      \date('H_i');

    // Output CSV-specific headers
    \header('Pragma: public');
    \header('Expires: 0');
    \header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    \header('Cache-Control: private', false);
    \header('Content-Type: application/octet-stream');
    \header('Content-Disposition: attachment; filename="'.$filename.'.csv";');
    \header('Content-Transfer-Encoding: binary');

    // Stream the CSV data
    exit($string);
  }
}
