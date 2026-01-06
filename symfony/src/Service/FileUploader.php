<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader {

    public function __construct(private string $targetDirectory, private SluggerInterface $slugger) {}


    public function upload(UploadedFile $file, string $subDirectory, ?string $oldFilename = null): string {
        $orginalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($orginalFileName);
        $newFilename = $safeFilename. '-'.uniqid().'.'.$file->guessExtension();

        $fullPath = $this->getTargetDirectory().'/'.$subDirectory;

        $file->move($fullPath, $newFilename);

        if($oldFilename) {
            $this->remove($oldFilename, $subDirectory);
        }

        return $newFilename;
    }

    public function remove(string $filename, string $subDirectory): void {
        if(!$filename) return;

        $fullPath = $this->getTargetDirectory().'/'.$subDirectory.'/'.$filename;

        if(file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public function getTargetDirectory(): string {
        return $this->targetDirectory;
    }
}
