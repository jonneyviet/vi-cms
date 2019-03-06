<?php
namespace Packages\Translation\Middleware;

/**
* 
*/
use Closure;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application;
use Illuminate\View\Factory as ViewFactory;
use Packages\Translation\Repositories\LanguageRepository;
use Packages\Translation\UriLocalizer;

class TranslationMiddleware
{
	function __construct(UriLocalizer $uriLocalizer,Config $config, LanguageRepository $languageRepository,ViewFactory $viewFactory, Application $app)
	{
		$this->uriLocalizer=$uriLocalizer;
		$this->config=$config;
		$this->languageRepository=$languageRepository;
		$this->app=$app;
		$this->viewFactory=$viewFactory;
	}
	public function handle($request, Closure $next, $segment=0){
		if($request->method()!=="GET"){
			return $next($request);
		}
		$currentUrl    = $request->getUri();
        $uriLocale     = $this->uriLocalizer->getLocaleFromUrl($currentUrl, $segment);

        $defaultLocale = $this->config->get('app.locale');
        if($uriLocale===$defaultLocale){
        	return redirect()->to($this->uriLocalizer->cleanUrl($currentUrl));
        }
		if($uriLocale){
			$currentLanguage=$this->languageRepository->finbyLocale($uriLocale);
			$selectableLanguages = $this->languageRepository->allExcept($uriLocale);
			$altLocalizedUrls    = [];
			foreach ($selectableLanguages as $value) {
				$altLocalizedUrls    = [
					"locale"=>$value->locale,
					"name"=>$value->name,
					"url"=>$this->uriLocalizer->localize($currentUrl, $value->locale, $segment)
				];
			}
			 // Set app locale
			$this->app->setLocale($uriLocale);
            // Share language variable with views:
            $this->viewFactory->share('currentLanguage', $currentLanguage);
            $this->viewFactory->share('selectableLanguages', $selectableLanguages);
            $this->viewFactory->share('altLocalizedUrls', $altLocalizedUrls);

            // Set locale in session:
            if ($request->hasSession() && $request->session()->get('translation.locale') !== $uriLocale) {
                $request->session()->put('translation.locale', $uriLocale);
            }
            return $next($request);
		}
		return $next($request);
	}
}