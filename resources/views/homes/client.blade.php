@extends('layouts.app')

@section('content')
        <div class="container-fluid">
            <!-- .row -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="white-box">
                        <h3 class="box-title m-b-20" style="text-align: left;"><b>Latest Comments</b></h3>
                        <button class="btn btn-success m-t-0" style="float: right">See Full History</button>
                        <p><b>Steave : </b>Lorem ipsum dolor sit amet, consectetur adipisicing elit. </p>
                        <p><b>Steave : </b>Nam sit amet ex quis ipsum lacinia feugiat id finibus turpis. </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="white-box">
                        <h3 class="box-title m-b-0"><b>Current Step :</b> Step 2</h3>

                        <h4>Next Action Due Date:</h4>
                        <p class="m-b-40">March, 17, 2017</p>

                        <h4>Last Step Submitted:</h4>
                        <p class="m-b-40">Today at 10:02 a.m.</p>

                        <h4>Next Step:</h4>
                        <p class="m-b-40">Step 3</p>

                        <h4>Previous Step:</h4>
                        <p class="">Step 1</p>
                    </div>
                    <div class="white-box">
                        <h3 class="box-title m-b-0"><b>Application Info</b></h3>

                        <h4>Responsible:</h4>
                        <p class="m-b-10">Staff 1</p>

                        <h4>Application Started:</h4>
                        <p class="m-b-40">Feb, 01, 2017</p>

                        <h4>Applicant:</h4>
                        <p class="m-b-40">Lorem Ipsum</p>

                        <h4>Applicant’s Representative:</h4>
                        <p class="">Lorem Ipsum</p>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="white-box">
                        <h3 class="box-title m-b-0"><b>Active Form :</b> Form 3</h3>
                        <h4>Items to be reviewed</h4>
                        <div class="table-responsive">
                            <table id="activeform" class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <h5 class="m-t-0"><b>Question</b></h5>
                                        Lorem Ipsum Dolor
                                        <h5><b>Answer</b></h5>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                    </td>
                                    <td>
                                        <button class="btn btn-success btn-circle"><i class="fa fa-pencil"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5 class="m-t-0"><b>Question</b></h5>
                                        Lorem Ipsum Dolor
                                        <h5><b>Answer</b></h5>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                    </td>
                                    <td>
                                        <button class="btn btn-success btn-circle"><i class="fa fa-pencil"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5 class="m-t-0"><b>Question</b></h5>
                                        Lorem Ipsum Dolor
                                        <h5><b>Answer</b></h5>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                    </td>
                                    <td>
                                        <button class="btn btn-success btn-circle"><i class="fa fa-pencil"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="white-box">
                        <h3 class="box-title m-b-0"><b>All Forms</b></h3>
                        <div class="table-responsive">
                            <table id="allforms" class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Form</th>
                                    <th>Status</th>
                                    <th style="width: 120px">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Form 1</td>
                                    <td>Approved</td>
                                    <td>
                                        <button class="btn btn-warning btn-circle"><i class="fa fa-pencil"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Form 2</td>
                                    <td>Approved</td>
                                    <td>
                                        <button class="btn btn-warning btn-circle"><i class="fa fa-pencil"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Form 3</td>
                                    <td>Current</td>
                                    <td>
                                        <button class="btn btn-warning btn-circle"><i class="fa fa-pencil"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Form 8</td>
                                    <td>Blank</td>
                                    <td>
                                        <button class="btn btn-warning btn-circle"><i class="fa fa-pencil"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Form 9</td>
                                    <td>Blank</td>
                                    <td>
                                        <button class="btn btn-warning btn-circle"><i class="fa fa-pencil"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="white-box">
                        <h3 class="box-title m-b-0"><b>Reports</b></h3>
                        <div class="table-responsive">
                            <table id="allforms" class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Report</th>
                                    <th style="width: 120px">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Report 1</td>
                                    <td>
                                        <button class="btn btn-warning btn-circle"><i class="fa fa-eye"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Report 2</td>
                                    <td>
                                        <button class="btn btn-warning btn-circle"><i class="fa fa-eye"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Report 3</td>
                                    <td>
                                        <button class="btn btn-warning btn-circle"><i class="fa fa-eye"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <!-- .right-sidebar -->
            <div class="right-sidebar">
                <div class="slimscrollright">
                    <div class="rpanel-title"> Service Panel <span><i class="ti-close right-side-toggle"></i></span> </div>
                    <div class="r-panel-body">
                        <ul>
                            <li><b>Layout Options</b></li>
                            <li>
                                <div class="checkbox checkbox-info">
                                    <input id="checkbox1" type="checkbox" class="fxhdr">
                                    <label for="checkbox1"> Fix Header </label>
                                </div>
                            </li>

                            <li>
                                <div class="checkbox checkbox-success">
                                    <input id="checkbox4" type="checkbox" class="open-close">
                                    <label for="checkbox4"> Toggle Sidebar </label>
                                </div>
                            </li>
                        </ul>
                        <ul id="themecolors" class="m-t-20">
                            <li><b>With Light sidebar</b></li>
                            <li><a href="javascript:void(0)" theme="default" class="default-theme">1</a></li>
                            <li><a href="javascript:void(0)" theme="green" class="green-theme">2</a></li>
                            <li><a href="javascript:void(0)" theme="gray" class="yellow-theme">3</a></li>
                            <li><a href="javascript:void(0)" theme="blue" class="blue-theme">4</a></li>
                            <li><a href="javascript:void(0)" theme="purple" class="purple-theme">5</a></li>
                            <li><a href="javascript:void(0)" theme="megna" class="megna-theme">6</a></li>
                            <li><b>With Dark sidebar</b></li>
                            <br/>
                            <li><a href="javascript:void(0)" theme="default-dark" class="default-dark-theme">7</a></li>
                            <li><a href="javascript:void(0)" theme="green-dark" class="green-dark-theme">8</a></li>
                            <li><a href="javascript:void(0)" theme="gray-dark" class="yellow-dark-theme">9</a></li>
                            <li><a href="javascript:void(0)" theme="blue-dark" class="blue-dark-theme working">10</a></li>
                            <li><a href="javascript:void(0)" theme="purple-dark" class="purple-dark-theme">11</a></li>
                            <li><a href="javascript:void(0)" theme="megna-dark" class="megna-dark-theme">12</a></li>
                        </ul>
                        <ul class="m-t-20 chatonline">
                            <li><b>Chat option</b></li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/varun.jpg" alt="user-img" class="img-circle"> <span>Varun Dhavan <small class="text-success">online</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/genu.jpg" alt="user-img" class="img-circle"> <span>Genelia Deshmukh <small class="text-warning">Away</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/ritesh.jpg" alt="user-img" class="img-circle"> <span>Ritesh Deshmukh <small class="text-danger">Busy</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/arijit.jpg" alt="user-img" class="img-circle"> <span>Arijit Sinh <small class="text-muted">Offline</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/govinda.jpg" alt="user-img" class="img-circle"> <span>Govinda Star <small class="text-success">online</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/hritik.jpg" alt="user-img" class="img-circle"> <span>John Abraham<small class="text-success">online</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/john.jpg" alt="user-img" class="img-circle"> <span>Hritik Roshan<small class="text-success">online</small></span></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><img src="../plugins/images/users/pawandeep.jpg" alt="user-img" class="img-circle"> <span>Pwandeep rajan <small class="text-success">online</small></span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.right-sidebar -->
        </div>
        <!-- /.container-fluid -->

@endsection
