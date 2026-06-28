<?php

namespace App\Http\Controllers\Gd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Certificate;
use App\Jobs\DispatchCertificateEmail;

class CertificateGeneratorController extends Controller
{
    public function index()
    {
        return view('gd.certificate-generator');
    }

    public function process(Request $request)
    {
        $request->validate([
            'template' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'names_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            'x_pos' => 'required|numeric',
            'y_pos' => 'required|numeric',
            'font_size' => 'nullable|numeric',
            'font_color' => 'nullable|string',
            'font_family' => 'nullable|string',
            'font_weight' => 'nullable|string',
            'font_style' => 'nullable|string',
        ]);

        $xPos = $request->input('x_pos');
        $yPos = $request->input('y_pos');
        $fontSize = (int) $request->input('font_size', 48);
        $fontColor = $request->input('font_color', '#000000');
        $fontFamily = $request->input('font_family', 'Arial');
        $fontWeight = $request->input('font_weight', 'normal');
        $fontStyle = $request->input('font_style', 'normal');

        $templatePath = $request->file('template')->getPathname();
        $namesFilePath = $request->file('names_file')->getPathname();

        // Ensure temp directories exist
        $tempDir = storage_path('app/temp_certs_' . time());
        $savedCertsDir = storage_path('app/public/certificates'); // Permanent path for emails
        File::makeDirectory($tempDir, 0755, true);
        if (!File::exists($savedCertsDir)) {
            File::makeDirectory($savedCertsDir, 0755, true);
        }

        try {
            // Read Excel/CSV file
            $spreadsheet = IOFactory::load($namesFilePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $participants = [];
            // Assume Row 0 might be header. We'll check if first row contains 'name'
            $startRow = 0;
            if (isset($rows[0][0]) && strtolower(trim($rows[0][0])) === 'name') {
                $startRow = 1;
            }

            for ($i = $startRow; $i < count($rows); $i++) {
                $row = $rows[$i];
                $name = trim($row[0] ?? '');
                $email = trim($row[1] ?? ''); // Assuming email is second column

                if (!empty($name)) {
                    $participants[] = [
                        'name' => $name,
                        'email' => $email
                    ];
                }
            }

            if (empty($participants)) {
                return back()->with('error', 'No participants found in the uploaded file.');
            }

            // Init Image Manager
            $manager = new ImageManager(new Driver());
            $generatedFiles = [];

            // 50+ Microsoft Word Font Mappings
            $fontMap = [
                'Agency FB' => ['regular' => 'AGENCYR.TTF', 'bold' => 'AGENCYB.TTF'],
                'Algerian' => ['regular' => 'ALGER.TTF'],
                'Arial' => ['regular' => 'arial.ttf', 'bold' => 'arialbd.ttf', 'italic' => 'ariali.ttf', 'bold_italic' => 'arialbi.ttf'],
                'Arial Black' => ['regular' => 'ariblk.ttf'],
                'Book Antiqua' => ['regular' => 'BKANT.TTF', 'bold' => 'ANTQUAB.TTF', 'italic' => 'ANTQUAI.TTF', 'bold_italic' => 'ANTQUABI.TTF'],
                'Bookman Old Style' => ['regular' => 'BOOKOS.TTF', 'bold' => 'BOOKOSB.TTF', 'italic' => 'BOOKOSI.TTF', 'bold_italic' => 'BOOKOSBI.TTF'],
                'Bradley Hand ITC' => ['regular' => 'BRADHITC.TTF'],
                'Britannic Bold' => ['regular' => 'BRITANIC.TTF'],
                'Broadway' => ['regular' => 'BROADW.TTF'],
                'Calibri' => ['regular' => 'calibri.ttf', 'bold' => 'calibrib.ttf', 'italic' => 'calibrii.ttf', 'bold_italic' => 'calibriz.ttf'],
                'Castellar' => ['regular' => 'CASTELAR.TTF'],
                'Century' => ['regular' => 'CENTURY.TTF'],
                'Century Gothic' => ['regular' => 'GOTHIC.TTF', 'bold' => 'GOTHICB.TTF', 'italic' => 'GOTHICI.TTF', 'bold_italic' => 'GOTHICBI.TTF'],
                'Chiller' => ['regular' => 'CHILLER.TTF'],
                'Colonna MT' => ['regular' => 'COLONNA.TTF'],
                'Comic Sans MS' => ['regular' => 'comic.ttf', 'bold' => 'comicbd.ttf', 'italic' => 'comici.ttf', 'bold_italic' => 'comicz.ttf'],
                'Consolas' => ['regular' => 'consola.ttf', 'bold' => 'consolab.ttf', 'italic' => 'consolai.ttf', 'bold_italic' => 'consolaz.ttf'],
                'Constantia' => ['regular' => 'constan.ttf', 'bold' => 'constanb.ttf', 'italic' => 'constani.ttf', 'bold_italic' => 'constanz.ttf'],
                'Cooper Black' => ['regular' => 'COOPBL.TTF'],
                'Copperplate Gothic Bold' => ['regular' => 'COPRGTB.TTF'],
                'Corbel' => ['regular' => 'corbel.ttf', 'bold' => 'corbelb.ttf', 'italic' => 'corbeli.ttf', 'bold_italic' => 'corbelz.ttf'],
                'Courier New' => ['regular' => 'cour.ttf', 'bold' => 'courbd.ttf', 'italic' => 'couri.ttf', 'bold_italic' => 'courbi.ttf'],
                'Curlz MT' => ['regular' => 'CURLZ___.TTF'],
                'Elephant' => ['regular' => 'ELEPHNT.TTF', 'italic' => 'ELEPHNTI.TTF'],
                'Franklin Gothic Medium' => ['regular' => 'framd.ttf', 'italic' => 'framdit.ttf'],
                'Gabriola' => ['regular' => 'Gabriola.ttf'],
                'Gadugi' => ['regular' => 'gadugi.ttf', 'bold' => 'gadugib.ttf'],
                'Garamond' => ['regular' => 'gara.ttf', 'bold' => 'garabd.ttf', 'italic' => 'garait.ttf'],
                'Georgia' => ['regular' => 'georgia.ttf', 'bold' => 'georgiab.ttf', 'italic' => 'georgiai.ttf', 'bold_italic' => 'georgiaz.ttf'],
                'Impact' => ['regular' => 'impact.ttf'],
                'Ink Free' => ['regular' => 'Inkfree.ttf'],
                'Leelawadee UI' => ['regular' => 'LeelawUI.ttf'],
                'Lucida Console' => ['regular' => 'lucon.ttf'],
                'Malgun Gothic' => ['regular' => 'malgun.ttf', 'bold' => 'malgunbd.ttf'],
                'Microsoft Sans Serif' => ['regular' => 'micross.ttf'],
                'Myanmar Text' => ['regular' => 'mmrtext.ttf'],
                'Palatino Linotype' => ['regular' => 'pala.ttf', 'bold' => 'palab.ttf', 'italic' => 'palai.ttf', 'bold_italic' => 'palabi.ttf'],
                'Segoe Print' => ['regular' => 'segoepr.ttf', 'bold' => 'segoeprb.ttf'],
                'Segoe Script' => ['regular' => 'segoesc.ttf', 'bold' => 'segoescb.ttf'],
                'Segoe UI' => ['regular' => 'segoeui.ttf', 'bold' => 'segoeuib.ttf', 'italic' => 'segoeuii.ttf', 'bold_italic' => 'segoeuiz.ttf'],
                'Segoe UI Black' => ['regular' => 'seguibl.ttf'],
                'Sylfaen' => ['regular' => 'sylfaen.ttf'],
                'Symbol' => ['regular' => 'symbol.ttf'],
                'Tahoma' => ['regular' => 'tahoma.ttf', 'bold' => 'tahomabd.ttf'],
                'Times New Roman' => ['regular' => 'times.ttf', 'bold' => 'timesbd.ttf', 'italic' => 'timesi.ttf', 'bold_italic' => 'timesbi.ttf'],
                'Trebuchet MS' => ['regular' => 'trebuc.ttf', 'bold' => 'trebucbd.ttf', 'italic' => 'trebucit.ttf', 'bold_italic' => 'trebucbi.ttf'],
                'Verdana' => ['regular' => 'verdana.ttf', 'bold' => 'verdanab.ttf', 'italic' => 'verdanai.ttf', 'bold_italic' => 'verdanaz.ttf'],
                'Webdings' => ['regular' => 'webdings.ttf'],
                'Wingdings' => ['regular' => 'wingding.ttf']
            ];

            // Map the font weights and styles to standard variant names
            $variant = 'regular';
            if ($fontWeight === 'bold' && $fontStyle === 'italic') $variant = 'bold_italic';
            elseif ($fontWeight === 'bold') $variant = 'bold';
            elseif ($fontStyle === 'italic') $variant = 'italic';
            
            // Resolve the exact TTF file from Windows Fonts
            $fontPathToUse = null;
            if (isset($fontMap[$fontFamily])) {
                $mappedFile = $fontMap[$fontFamily][$variant] ?? $fontMap[$fontFamily]['regular'];
                $winPath = "C:\\Windows\\Fonts\\" . $mappedFile;
                if (File::exists($winPath)) {
                    $fontPathToUse = $winPath;
                }
            }

            // Fallback just in case (we copied Arial earlier to public/fonts)
            if (!$fontPathToUse && File::exists(public_path('fonts/Arial.ttf'))) {
                $fontPathToUse = public_path('fonts/Arial.ttf');
            }

            $eventName = "Automated Event"; // Could be dynamically fetched if added to form

            foreach ($participants as $participant) {
                $name = $participant['name'];
                $email = $participant['email'];

                // 1. Generate UUID & DB Record
                $uuid = Str::uuid()->toString();
                Certificate::create([
                    'uuid' => $uuid,
                    'participant_name' => $name,
                    'email' => $email,
                    'event_name' => $eventName
                ]);



                // 3. Image Processing
                $image = $manager->read($templatePath);

                // Add text
                $image->text($name, $xPos, $yPos, function ($font) use ($fontSize, $fontColor, $fontPathToUse) {
                    if (is_string($fontPathToUse) && file_exists($fontPathToUse)) {
                        $font->file($fontPathToUse);
                        $font->size($fontSize);
                    } else {
                        // In v3, if we don't have a valid TTF string, we skip setting the filename 
                        // so it falls back to the default internal GD font natively.
                        $font->size($fontSize);
                    }
                    $font->color($fontColor);
                    $font->align('center');
                    $font->valign('middle');
                });



                // Save Final Certificate
                $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $name);
                $fileName = $safeName . '_' . $uuid . '_certificate.jpg';
                
                // Save to temp dir for zip
                $tempFilePath = $tempDir . '/' . $fileName;
                $image->save($tempFilePath);
                $generatedFiles[] = $tempFilePath;

                // Save permanent copy for email job
                $permFilePath = $savedCertsDir . '/' . $fileName;
                File::copy($tempFilePath, $permFilePath);

                // Dispatch Email Job if email exists
                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    DispatchCertificateEmail::dispatch($email, $name, $permFilePath, $eventName);
                }
            }

            // Create ZIP file
            $zip = new ZipArchive();
            $zipFileName = 'Certificates_' . time() . '.zip';
            $zipPath = storage_path('app/' . $zipFileName);

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($generatedFiles as $file) {
                    $zip->addFile($file, basename($file));
                }
                $zip->close();
            } else {
                throw new \Exception("Could not create ZIP file.");
            }

            // Clean up temp directory
            File::deleteDirectory($tempDir);

            // Return download response and delete file after send
            return response()->download($zipPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Clean up temp directory in case of error
            if (File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
            return back()->with('error', 'Error generating certificates: ' . $e->getMessage());
        }
    }
}
