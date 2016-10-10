<?php
namespace Fireguard\Report\Exporters;

use Fireguard\Report\Contracts\ExporterContract;
use Fireguard\Report\Contracts\ReportContract;

class HtmlExporter extends AbstractExporter implements ExporterContract
{

    public function configure(array $config = [])
    {
        $this->extension = '.html';
        $defaultConfig = $this->getDefaultConfiguration();
        $this->config = array_replace_recursive($defaultConfig['html'] , $config);
        return $this;
    }

    public function generate(ReportContract $report)
    {
        $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>'.$this->fileName.'</title></head>';
        $html.= '<body style="background-color: #ffffff;">';
        $html.= $this->getProcessedHeader($report).$report->getContent().$this->getProcessedFooter($report);
        $html.= '</body></html>';
        return $this->saveFile($html);
    }

    public function getProcessedHeader(ReportContract $report)
    {
        return $this->processInlineHtml($report->getHeader());
    }

    public function getProcessedFooter(ReportContract $report)
    {
        return $this->processInlineHtml($report->getFooter());
    }

    public function saveFile($content)
    {
        file_put_contents($this->getFullPath(), $content);
        return $this->getFullPath();
    }

    protected function processInlineHtml($html)
    {
        $clearHtml = str_replace('"', '\'', $html);
        $clearHtml = str_replace("@{{", '', $clearHtml);
        $clearHtml = str_replace('numPage', ' 1 ', $clearHtml);
        $clearHtml = str_replace('totalPages', ' 1 ', $clearHtml);
        return $this->compress(str_replace("}}", '', $clearHtml));
    }

}