
                <div class="col-md-12" id="registerForm">
                    <h1>User Profile</h1>
                    
                    <div class="clear"></div>
                    
                    <table id="box-table-a">
                        <thead>
                            <tr>
                                <th colspan="2">User Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($output['user'] as $key => $value){ ?>
                            <tr>
                                    <?php 
                                    $noval = false;
                                    
                                    if($key == "dateCreated" || $key == "lastAccessed"){
                                        $key = $key == "dateCreated" ? "date created" : "last accessed";
                                        $noval = true;
                                        $value = date("F dS, Y", $value);
                                    }
                                    
                                    if($key == "ipAddress"){
                                        $key = "IP Address";
                                    }
                                    
                                    if($key == "gender"){
                                        $noval = true;
                                        $value = $value == "M" ? "Male" : "Female";
                                    }
                                    
                                    if($noval == false){
                                        $value = $value;
                                    }
                                    ?>
                                
                                <td><?php echo ucwords($key); ?></td>
                                <td><?php echo $value; ?></td> 
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    
                    <div class="clear"></div>
                </div>