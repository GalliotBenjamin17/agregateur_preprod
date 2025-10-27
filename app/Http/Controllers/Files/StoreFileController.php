<?php

namespace App\Http\Controllers\Files;

use App\Helpers\ActivityHelper;
use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StoreFileController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'file' => 'file',
        ]);

        //Vérification que le fichier peut-être accepté
        $available = $this->getContentType(extension: $request->file('file')->extension(), check: true);

        if (! $available) {
            Session::flash('alert', "Le fichier ne peut pas être ajouté car son extension n'est pas acceptée sur la plateforme.");

            return back();
        }

        $url = $request->file->store('files');

        $file = File::create([
            'name' => $request->input('name'),
            'related_id' => $request->input('model_id'),
            'related_type' => $request->input('model_class'),
            'path' => $url,
            'created_by' => $request->user()->id,
            'extension' => $request->file('file')->extension(),
            'content_type' => $this->getContentType(extension: $request->file('file')->extension()),
        ]);

        $file->load([
            'related',
        ]);

        ActivityHelper::push(
            performedOn: $file->related ? $file->related : $file,
            title: "Ajout d'un nouveau fichier",
            description: $file->related ? 'Modèle: '.$file->related->name : null,
            url: $file->related ? (method_exists($file->related, 'redirectRouter') ? $file->related->redirectRouter() : null) : route('files.show', ['file' => $file->slug])
        );

        Session::flash('success', 'Le fichier a été ajouté.');

        return back();
    }

    public function getContentType(string $extension, bool $check = false)
    {
        $contentTypes = [
            'aac' => 'audio/aac',
            'abw' => 'application/x-abiword',
            'arc' => 'application/octet-stream',
            'avi' => 'video/x-msvideo',
            'azw' => 'application/vnd.amazon.ebook',
            'bin' => 'application/octet-stream',
            'bmp' => 'image/bmp',
            'bz' => 'application/x-bzip',
            'bz2' => 'application/x-bzip2',
            'csh' => 'application/x-csh',
            'css' => 'text/css',
            'csv' => 'text/csv',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'eot' => 'application/vnd.ms-fontobject',
            'epub' => 'application/epub+zip',
            'gif' => 'image/gif',
            'htm html' => 'text/html',
            'ico' => 'image/x-icon',
            'ics' => 'text/calendar',
            'jar' => 'application/java-archive',
            'jpeg jpg' => 'image/jpeg',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'mid midi' => 'audio/midi',
            'mpeg' => 'video/mpeg',
            'mpkg' => 'application/vnd.apple.installer+xml',
            'odp' => 'application/vnd.oasis.opendocument.presentation',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'oga' => 'audio/ogg',
            'ogv' => 'video/ogg',
            'ogx' => 'application/ogg',
            'otf' => 'font/otf',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'rar' => 'application/x-rar-compressed',
            'rtf' => 'application/rtf',
            'sh' => 'application/x-sh',
            'svg' => 'image/svg+xml',
            'swf' => 'application/x-shockwave-flash',
            'tar' => 'application/x-tar',
            'tiff' => 'image/tiff',
            'ts' => 'application/typescript',
            'ttf' => 'font/ttf',
            'vsd' => 'application/vnd.visio',
            'wav' => 'audio/x-wav',
            'weba' => 'audio/webm',
            'webm' => 'video/webm',
            'webp' => 'image/webp',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'xhtml' => 'application/xhtml+xml',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xml' => 'application/xml',
            'xul' => 'application/vnd.mozilla.xul+xml',
            'zip' => 'application/zip',
            '3gp' => 'video/3gpp audio/3gpp dans le cas où le conteneur ne comprend pas de vidéo',
            '3g2' => 'video/3gpp2 audio/3gpp2 dans le cas où le conteneur ne comprend pas de vidéo',
            '7z' => 'application/x-7z-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
        ];

        if ($check) {
            return in_array($extension, array_keys($contentTypes));
        }

        return $contentTypes[$extension];
    }
}
