<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Autism Fitness Books Shop</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 150vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 32px;
            }

            .content {
                padding-top: 40%;
                text-align: center;
                display: block;
                width: 100%;
            }

            .title {
                font-size: 75px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            .logo{
                position: absolute;
                top: 9px;
                left: 20px;
            }
            .product  {
                width: 20%;
                display: inline-block;
                padding: 30px;
            }
            .product-card__image-wrapper{
                height: 235px;
                margin-bottom: 60px;
                
            }
            .product-card__info{
                margin-top: 55%;
                background-color: #fff;
            }
            .product-card__name{
                font-weight: 700;
                color: #1a1a1a;
                white-space: normal
            }
            .product-card__brand, .product-card__price {
                font-size: 1em;
            }
            .product-card__image-wrapper img{ 
                display: block;
                width:100%;
             }
            .site-footer{
                background-color: #333;
                color: rgba(255,255,255,0.6);
             }
             .page-width{
                max-width: 1180px;
                margin: 0 auto;
             }
             .site-footer__section-title {
                color: #fff;
                font-size: 1.71429em;
            }
            .site-footer__section{
                padding-bottom: 40px;
            }

            .site-footer{
                padding-top: 40px;
                height: 350px;
            }
            .flex-item{
                width:50%;
                float: left;
            }
            .site-footer__section-title a {
                padding-left:10px;
            }
            .flex-item span a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            }
            form { 
            margin-top: 15px; 
            }
            input[type="image"] {
                width: 100%;
            }

        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height ">
            
            <img src="https://cdn.shopify.com/s/files/1/2336/2043/files/newlogo2017_450x.png?v=1504707861" class="logo">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>

            @endif
            <div style="width:100%; padding: 1px;background-color: #636b6f; position: absolute; top: 87px; margin: 0 auto;"></div>
            <br/>
            <div class="content">
                <div class="title m-b-md">
                    Products
                </div>

                <!--                
                <div class="product">
                 <div class="product-card__image-wrapper">
                    <img src="//cdn.shopify.com/s/files/1/2336/2043/products/EC_AUTFit_Bike_EBK_CVR_480x480.jpg?v=1504653614" alt="Autism Fitness Bike to the Future E-Book" class="product-card__image">
                  </div>
                 <div class="product-card__info">
                   <div class="product-card__name">Autism Fitness Bike to the Future E-Book</div>
                     <div class="product-card__price">    
                         <span class="visually-hidden">Regular price</span>
                         <span class="money" doubly-currency-usd="2495" doubly-currency="USD">$24.95 USD</span>
                     </div>

                 </div>
                     <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="Y93EYCFL5YZ5Y">
                            <input type="image" src="http://67.205.164.32/storage/images/paypaltocart.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                            <input type="hidden" name="cbt" value="Click here to download your eBook">
                            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                    </form>     
                </div>


                <div class="product">
                 <div class="product-card__image-wrapper">
                    <img src="//cdn.shopify.com/s/files/1/2336/2043/products/EC_AF_EBK_TOC_480x480.png?v=1504653623" alt="Autism Fitness E-Book" class="product-card__image">
                  </div>
                 <div class="product-card__info">
                   <div class="product-card__name">Autism Fitness E-Book</div>
                     <div class="product-card__price">    
                         <span class="visually-hidden">Regular price</span>
                         <span class="money" doubly-currency-usd="2495" doubly-currency="USD">$24.95 USD</span>
                     </div>
                            
                 </div>  
                    <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="B4D2ARU27V3XU">
                    <input type="image" src="http://67.205.164.32/storage/images/paypaltocart.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <input type="hidden" name="cbt" value="Click here to download your eBook">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                    </form>
                </div>

                <div class="product">
                 <div class="product-card__image-wrapper">
                    <img src="//cdn.shopify.com/s/files/1/2336/2043/products/EC_AUTFitnessInClass_EBK_CVR_480x480.png?v=1504653618" alt="Autism Fitness in MY Classroom E-Book" class="product-card__image">
                  </div>
                 <div class="product-card__info">
                   <div class="product-card__name">Autism Fitness in MY Classroom E-Book</div>
                     <div class="product-card__price">    
                         <span class="visually-hidden">Regular price</span>
                         <span class="money" doubly-currency-usd="2495" doubly-currency="USD">$24.95 USD</span>
                     </div>
                            
                 </div>
                 <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="UUXMBSNCC2LHY">
                    <input type="image" src="http://67.205.164.32/storage/images/paypaltocart.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <input type="hidden" name="cbt" value="Click here to download your eBook">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>
                </div>
                -->

                <footer class="site-footer" role="contentinfo">
                    <div class="page-width">
                        <div class="flex-footer">
                            <div class="flex-item">
                                <h4 class="h1 site-footer__section-title">Autism Fitness</h4>
                                <span><a href="https://autismfitness.com/">Learm more about us</a></span>
                            </div> 
                            <div class="flex-item">
                                <h4 class="h1 site-footer__section-title">Follow us </h4>
                                <!-- Go to www.addthis.com/dashboard to generate a new set of sharing buttons -->
                                <a href="https://www.facebook.com/TheAutismFitness" target="_blank"><img src="https://cache.addthiscdn.com/icons/v2/thumbs/32x32/facebook.png" border="0" alt="Facebook"/></a>
                                <a href="https://twitter.com/AutismFitness" target="_blank"><img src="https://cache.addthiscdn.com/icons/v2/thumbs/32x32/twitter.png" border="0" alt="Twitter"/></a>
                                <a href="https://www.instagram.com/theautismfitness/" target="_blank"><img src="https://cache.addthiscdn.com/icons/v2/thumbs/32x32/instagram.png" border="0" alt="Google+"/></a>
                                <a href="http://youtube.com/TheAutismFitness" target="_blank"><img src="https://cache.addthiscdn.com/icons/v2/thumbs/32x32/youtube.png" border="0" alt="youtube link"/></a>
                            </div> 
                        </div>
                        
                    </div>
                </footer>

            </div>
        </div>
    </body>
</html>
