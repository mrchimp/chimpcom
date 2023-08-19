interface Result {
  [key: string]: string | string[];
}

export default (function () {
  var results: Result = {};
  var hash: string[];

  if (window.location.href.indexOf('?') !== -1) {
    var querystring = window.location.href.slice(window.location.href.indexOf('?') + 1);
    var hashes = querystring.split('&');

    for (var i = 0; i < hashes.length; i++) {
      hash = hashes[i].split('=');

      if (hash[0].slice(-2) === '[]') {
        const key = hash[0].slice(0, -2);

        if (!Array.isArray(results[key])) {
          results[key] = [];
        }

        // @ts-ignore
        results[key].push(hash[1]);
      } else {
        results[hash[0]] = hash[1];
      }
    }
  }

  return results;
})();
