<?hh //strict


\__("Without text-domain");
\__("With text-domain", "phmm");
__("Without text-domain");
__("With text-domain", "phmm");

__("Without text-domain",);

invariant(false, 'blub');
