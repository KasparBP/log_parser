<?php
use Riak\Connection;
use Riak\MapReduce\Functions\JavascriptFunction;
use Riak\MapReduce\Input\BucketInput;
use Riak\MapReduce\MapReduce;
use Riak\MapReduce\Phase\MapPhase;
use Riak\MapReduce\Phase\ReducePhase;

$connection  = new Connection('localhost');

$mrinput = new BucketInput('phpriak_log_access');
$jsmapfunc = JavascriptFunction::anon('function (v) {
    var r = [];
    if(!v.not_found) {
        var object = Riak.mapValuesJson(v)[0];
        // Extract date ignore time
        var date = object["date"]["date"].split(" ")[0];
        var o = {};
        o[date] = 1;
        r.push(o);
    }
    return r;
}');

$jsredfunc = JavascriptFunction::anon('function (values) {
    var result = {};
    if (values.length > 0) {
        for (value in values) {
            for(var date in values[value]) {
                var count = values[value][date];
                if (date in result) result[date] += count;
                else result[date] = count;
            }
        }
    }
    return [result];
}');

$mr = new MapReduce($connection);
$mr ->addPhase(new MapPhase($jsmapfunc, false))
    ->addPhase(new ReducePhase($jsredfunc, false))
    ->setInput($mrinput);
/** @var $result \Riak\MapReduce\Output\Output[] */
$result = $mr->run();
print_r($result[0]->getValue());
/*var_dump($result[0]);
var_dump($result);*/