var globalCacheForjQueryReplacement = window.jQuery;
window.jQuery = window.kQuery;
window.jQuery = globalCacheForjQueryReplacement;
globalCacheForjQueryReplacement = undefined;