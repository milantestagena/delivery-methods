<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="/assets/css/bootstrap.css" />
        <link rel="stylesheet" href="/assets/css/style.css" />
        <script type="text/javascript" src="/assets/js/jquery-2.1.4.js"></script>
        <script type="text/javascript" src="/assets/js/bootstrap.js"></script>
        <script type="text/javascript" src="/npm/node_modules/angular/angular.js"></script>
        <script type="text/javascript" src="/npm/node_modules/angular/angular-route.js"></script>
        <script type="text/javascript" src="/assets/js/angular.js"></script>
    </head>
    <body>
        <div id="container-fluid" ng-app="deliveryApp" ng-controller="deliveryCtrl">
            <div class="col-md-8 col-md-offset-2 ">
                <form class="form-horizontal" role="form"  id ="delivery_form"  name="delivery_form"    ng-submit="submitForm()">
                    <div ng-if ="deliveryMethods.length > 0"  ng-repeat="(indexDeliveryMethod, deliveryMethod) in deliveryMethods" ng-if = "deliveryMethod" ng-init = "$ordNum = indexDeliveryMethod + 1">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="accordion" class="panel-group">
                                    <div class="panel panel-default">
                                        <div class="panel-heading"  ng-mouseover="deliveryMethod.showRanges = true" ng-mouseleave="deliveryMethod.showRanges = false" >
                                            <div class="panel-title">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control " id="name-{{$ordNum}}" ng-model="deliveryMethod.name" />
                                                   
                                                </div>
                                                <div class="col-md-4">
                                                    <span class="col-md-4" ng-if ="(deliveryMethod.ranges.length == 1) && (deliveryMethod.ranges[0].from == deliveryMethod.ranges[0].to)">{{deliveryMethod.ranges[0].from}}</span>
                                                    <span class="col-md-8" ng-if ="(deliveryMethod.ranges.length > 1) || (deliveryMethod.ranges.length == 1 && deliveryMethod.ranges[0].from != deliveryMethod.ranges[0].to)">
                                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseRange{{$ordNum}}">Show Ranges </a>
                                                    </span>  
                                                    <span class="col-md-8"  ng-if ="deliveryMethod.showRanges && !((deliveryMethod.ranges.length > 1) || (deliveryMethod.ranges.length == 1 && deliveryMethod.ranges[0].from != deliveryMethod.ranges[0].to))">    
                                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseRange{{$ordNum}}">Add Range</a>
                                                    </span>

                                                </div>
                                                <div class="col-md-4">
                                                    <span  ng-if ="deliveryMethod.showRanges">    
                                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOptions{{$ordNum}}">Show options</a>
                                                    </span>
                                                    <span  ng-if ="(deliveryMethod.showRanges && $index > 0)">    
                                                        <a class="pull-right cursor-pointer" ng-click="deleteMe(deliveryMethods, $index)">Delete</a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="collapseRange{{$ordNum}}" class="panel-collapse collapse">

                                        <div class="panel-body">
                                            <div class="row">
                                                <div class ="form-inline ">
                                                    <div  ng-repeat="(indexRange, range) in deliveryMethod.ranges" ng-if = "range" ng-init = "$ordRangeNum = indexRange + 1">

                                                        <div class="col-md-12 range-row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">

                                                                    <label for="rangeFrom-{{$ordNum}}-{{$ordRangeNum}}">From:</label>
                                                                    <input type="number"  ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/" step="0.01"   class="form-control" id="rangeFrom-{{$ordNum}}-{{$ordRangeNum}}" ng-model="range.from" />
                                                                    
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="rangeTo-{{$ordNum}}-{{$ordRangeNum}}">To:</label>
                                                                    <input type="number" min = "{{range.from}}"  ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/" step="0.01" class="form-control"  id="rangeTo-{{$ordNum}}-{{$ordRangeNum}}" ng-model="range.to" />

                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 ">
                                                                <button  type = "button" class="btn btn-default" ng-click="deleteMe(deliveryMethod.ranges, $index, true)">Delete</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row margin-top-15">
                                                <div class="col-md-12 ">
                                                    <button type="button" class="btn btn-default pull-left pull-left" ng-click="add(deliveryMethod, 'ranges', 'range')">Add Range</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="collapseOptions{{$ordNum}}" class="panel-collapse collapse">

                                        <div class="panel-body ">

                                            <div class="form-group">

                                                <label class="col-md-5" for="url-{{$ordNum}}">Delivery URL:</label>
                                                <div class="col-md-7" >
                                                    <input type="text" class="form-control " id="url-{{$ordNum}}" ng-model="deliveryMethod.url" />
                                                </div>

                                            </div>
                                            <div class="form-group ">

                                                <label class="col-md-5 " for="url-{{$ordNum}}">Weight (accepted delivery in KG):</label>

                                                <div class ="col-md-7 delivery-options" >
                                                    <fieldset class="form-inline ">
                                                        <label for="from-{{$ordNum}}">From:</label>
                                                        <input type="number"  ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/" step="0.01" class="form-control" required id="weightFrom-{{$ordNum}}" ng-model="deliveryMethod.weight_from" />

                                                        <label for="to-{{$ordNum}}">To:</label>
                                                        <input type="number" min ="{{deliveryMethod.weight_from}}"  ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/" step="0.01" class="form-control" required id="weightTo-{{$ordNum}}" ng-model="deliveryMethod.weight_to" />
KG
                                                    </fieldset>
                                                </div>
                                            </div>


                                            <div class="form-group">

                                                <label class="col-md-5" for="notes-{{$ordNum}}">Notes:</label>
                                                <div class="col-md-7" >
                                                    <textarea class="form-control" id="notes-{{$ordNum}}" ng-model="deliveryMethod.notes"></textarea>
                                                </div>

                                            </div>

                                        </div>
                                    </div>



                                </div>
                            </div>                 


                        </div>
                    </div>
                    <div class ="col-md-12" ng-if ="deliveryMethods.length == 0">
                        Delivery methods not defined yet.
                    </div>
                    <div>
                        <button type="submit" class="btn btn-default pull-right" >Save</button>
                        <button type="button" class="btn btn-default  pull-right " ng-click="add(deliveryMethods, false, 'deliveryMethod')">Add</button>
                    </div>

                </form>
            </div>

        </div>
    </body>
</html>