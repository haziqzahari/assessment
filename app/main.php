<?php

    interface Subscription
    {
        public function subscribe($plan);
        public function unsubscribe();
    }

    interface ServerConnection
    {
        public function connectServer($server);
    }


    class User implements Subscription, ServerConnection
    {
        public $name; //string
        public $plan; //string 
        public $server; //array(object)

        public function subscribe($plan)
        {
            $sub = new Plan();
            $sub->plan = $this->plan;
            $this->plan = $sub->subscribe($plan);
        }

        public function unsubscribe()
        {
            print "Action => Cancelling plan to ".$this->plan.".\n";

            $sub = new Plan();
            $sub->plan = $this;
            $this->plan = $sub->unsubscribe();
            $this->server = null;

            print "Thank you for Using RunCloud.\n\n";
        }

        public function connectServer($server)
        {
            print "Action => Connecting to Server ".$server->name." ...\n";


            try {
                //if User is subscribed to Basic Plan and have 
                //connected server with max_server = 1
                if($this->plan == "Basic Plan" && !is_null($this->server))
                {
                    throw new Exception("Error => User Exceeded Server Limit for Current Plan : ".$this->plan.".");
                }
                //if user subscribed to Pro Plan
                else if(!is_null($this->plan))
                {
                    $conn = new Server(); 
                    
                    if(!is_null($this->server)) //no server is connected yet.
                    {
                        $conn->servers = $this->server;
                    }

                    $this->server = (array) $conn->connectServer($server);

                    print "Action => Connected to Server ".$server->name.".\n\n";

                    $this->printDetails();
                }
                else
                {
                    throw new Exception("Error => User is not subscribed to any plan.");
                }
            } catch (Exception $e) {
                print $e->getMessage()."\n\n";
            }
        }

        //print a table for Subscription Details
        public function printDetails()
        {
            $mask = "|  |%20s |%40s |  |\n";

            if($this->name != null)
            {
                printf($mask, "User's Name", $this->name);
                printf($mask, "Current Plan", $this->plan);

                printf($mask, "Connected Servers", "");
                foreach ((array)$this->server as $index => $server) {
                   foreach ($server as $key => $value) {
                    if($key != "servers")
                    {
                        printf($mask, $key, $value);
                    }
                   }
                }
            }

            print "\n\n";
        }
    }


    class Server implements ServerConnection
    {
        public $name;
        public $ipAddress;
        public $servers;

        //return array of servers
        public function connectServer($server)
        {
            
            if(!is_null($this->servers)) 
            {
                $server = (array) $server;
                
                $server = array(
                    $this->servers[0],
                    $server
                );

                return (array)$server;
            }
            else{
                return array((array)$server);
            }
            
        }

    }


    class Plan implements Subscription
    {
        public $name;
        
        public function subscribe($plan)
        {
            print "Action => Subscribing to ".$plan->name." ...\n";

            try {
                if($plan->name == "Basic Plan")
                {
                    $this->name = $plan->name;

                    print "Action => Subscribed to ".$plan->name.".\n\n";
                    
                }
                else
                {
                    $this->name = $plan->name;

                    print "Action => Upgraded to ".$plan->name.".\n\n";
                }
                return $this->name;
            } catch (Exception $e) {
                print $e->getMessage()."\n";
            }

        }

        public function unsubscribe()
        {
            return null;
        }
    }

    class BasicPlan extends Plan
    {
        public function __construct()
        {
            $this->name = "Basic Plan";
        }
    }

    class ProPlan extends Plan 
    {
        public function __construct()
        {
            $this->name = "Pro Plan";
        }
    }
?>