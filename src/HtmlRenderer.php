<?php

declare(strict_types=1);

namespace Vinicius\HtmlRenderer;

use League\Plates\Engine;
use League\Plates\Template\Template;

class HtmlRenderer
{
    private Template $template;
    private Engine $engine;
    private string $language;
    private string $templatePath;
    private string $viewsPath;
    private array $templateData = [];
    private array $headerData = [];
    private array $footerData = [];
    private array $contentData = [];
    private array $headData = [];
    private array $commonData = [];
    private string $body;
    private string $contentFolder;
    private string $header;
    private string $footer;
    private string $head;

    function __construct(string $templatePath, string $viewsPath, string $templateFile='template')
    {
        $this->engine = new Engine($templatePath);
        $this->engine->addFolder('views', $viewsPath);
        $this->viewsPath = $viewsPath;
        $this->template = $this->engine->make($templateFile);
        $this->templatePath = $templatePath;
        $this->setBody('body');
        $this->setHeader('header');
        $this->setFooter('footer');
        $this->setHead('head');
    }
    
    public function setBody(string $file): void
    {
        $this->body = 'views::'.$file.'/content';
        $this->contentFolder = $this->viewsPath.$file.'/';
    }
    public function setLanguage(string $language): void
    {
        $this->language = $language;
        $this->template->data(require $this->templatePath."template.{$language}.php");
    }
    public function setHeader(string $file):void
    {
        $this->header = 'headers/'.$file;
    }
    public function setFooter(string $file):void
    {
        $this->footer = 'footers/'.$file;
    }
    public function setHead(string $file):void
    {
        $this->head = 'heads/'.$file;
    }
    
    public function addHeadData(array $data):void
    {
        $this->headData = array_merge($this->headData, $data);
    }
    public function addCommonData(array $data):void
    {
        $this->commonData = array_merge($this->commonData, $data);
    }
    public function addTemplateData(array $data):void
    {
        $this->templateData = array_merge($this->templateData, $data);
    }
    public function addHeaderData(array $data):void
    {
        $this->headerData =  array_merge($this->headerData, $data);
    }
    public function addFooterData(array $data):void
    {
        $this->footerData =  array_merge($this->footerData, $data);
    }
    public function addContentData(array $data):void
    {
        $this->contentData =  array_merge($this->contentData, $data);
    }
    public function addFolder(string $name, string $folderPath): void
    {
        $this->engine->addFolder($name, $folderPath);
    }
    public function render(): string
    {
        if (isset($this->language)) {
            $this->template->data(require $this->templatePath."template.{$this->language}.php");
        }
        $this->retrieveContentData();
        $head__data=$this->headData+$this->templateData+$this->commonData;
        $header__data=$this->headerData+$this->templateData+$this->commonData;
        $footer__data=$this->footerData+$this->templateData+$this->commonData;
        $content__data=$this->contentData+$this->commonData;
        return $this->template->render(
            [
            'template__head'=>$this->head,
            'template__header'=>$this->header,
            'template__footer'=>$this->footer,
            'template__content'=>$this->body,
            ] +
            $this->commonData+$this->templateData +
            [
            'template__head__data'=>$head__data,
            'template__header__data'=>$header__data,
            'template__footer__data'=>$footer__data,
            'template__content__data'=>$content__data,
        ]);
    }
    protected function retrieveContentData(): void
    {
        if (isset($this->language)) {
            $this->template->data(require $this->templatePath."template.{$this->language}.php");
        }
        $this->addHeadData($this->retrieveFileData($this->contentFolder.'data.head.php'));
    }
    protected function retrieveFileData(string $filePath): array
    {
        if (file_exists($filePath)) {
            return require $filePath;
        }
        return [];
    }
}
