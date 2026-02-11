<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader {

    public function __construct(
        private readonly string $targetDirectory,
        private readonly SluggerInterface $slugger
    ) {}


    public function upload(UploadedFile $file, string $subDirectory, ?string $oldFilename = null): string {
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFileName);
        $newFilename = $safeFilename. '-'.uniqid().'.'.$file->guessExtension();

        $fullPath = $this->getTargetDirectory().'/'.$subDirectory;

        try {
            $file->move($fullPath, $newFilename);
        }catch(FileException $e){
            throw $e;
        }

        if($oldFilename) {
            $this->remove($oldFilename, $subDirectory);
        }

        return $newFilename;
    }

    public function remove(?string $filename, string $subDirectory): void {
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
