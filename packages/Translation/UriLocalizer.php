<?php
namespace Packages\Translation;

/**
* 
*/
use Illuminate\Http\Request;
use Packages\Translation\Repositories\LanguageRepository;
class UriLocalizer
{
	
	function __construct(Request $request,LanguageRepository $languageRepository)
	{
		$this->request=$request;
        $this->availableLocales = $languageRepository->availableLocales();
	}
	public function localeFromRequest($segment = 0)
    {
        $url = $this->request->getUri();
        return $this->getLocaleFromUrl($url, $segment);
    }
    public function localize($url, $locale, $segment = 0)
    {
        $cleanUrl  = $this->cleanUrl($url, $segment);
        $parsedUrl = $this->parseUrl($cleanUrl, $segment);

        // Check if there are enough segments, if not return url unchanged:

        if (count($parsedUrl['segments']) >= $segment) {
            array_splice($parsedUrl['segments'], $segment, 0, $locale);
        }
        return $this->pathFromParsedUrl($parsedUrl);
    }
	public function getLocaleFromUrl($url, $segment = 0)
    {
        return $this->parseUrl($url, $segment)['locale'];
    }
	protected function parseUrl($url, $segment = 0)
    {
        $parsedUrl             = parse_url($url);
        $parsedUrl['segments'] = array_values(array_filter(explode('/', $parsedUrl['path']), 'strlen'));
        $localeCandidate       = array_get($parsedUrl['segments'], $segment, false);
        $parsedUrl['locale']   = in_array($localeCandidate, $this->availableLocales) ? $localeCandidate : null;
        $parsedUrl['query']    = array_get($parsedUrl, 'query', false);
        $parsedUrl['fragment'] = array_get($parsedUrl, 'fragment', false);
        unset($parsedUrl['path']);//clear patch
        return $parsedUrl;
    }
    public function cleanUrl($url, $segment = 0)
    {
        $parsedUrl = $this->parseUrl($url, $segment);
        // Remove locale from segments:
        if ($parsedUrl['locale']) {
            unset($parsedUrl['segments'][$segment]);
            $parsedUrl['locale'] = false;
        }
        return $this->pathFromParsedUrl($parsedUrl);
    }
    public function setLocale($locale = null)
    {
        $locale = $this->request->segment(1);
    }
    /**
     *  Returns the uri for the given parsed url based on its segments, query and fragment
     *
     *  @return string
     */
    protected function pathFromParsedUrl($parsedUrl)
    {
        $path = '/' . implode('/', $parsedUrl['segments']);
        if ($parsedUrl['query']) {
            $path .= "?{$parsedUrl['query']}";
        }
        if ($parsedUrl['fragment']) {
            $path .= "#{$parsedUrl['fragment']}";
        }
        return $path;
    }
    protected function removeFrontSlash($path){
        return strlen($path)>0 && substr($path,0,1)==="/" ? substr($path,1):$path;
    }
    protected function removeTraillingSlash($path){
        return strlen($path)>0 && substr($path,-1)==="/" ? substr($path,0,-1):$path;
    }
}