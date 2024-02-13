
    <div id="qrcode<?=$id?>" style='width:200px;height:200;'></div>
    <script type="text/javascript">
                        var qrcode = new QRCode(document.getElementById("qrcode<?=$id?>"), {
                            text:"ban/<?=$id?>",
                            title: "location qr",
                           
                            titleFont: "bold 16px Arial",
                        titleColor: "#000000",
                        titleBackgroundColor: "#ffffff",
                        titleHeight: 35,
                        
                    
                            width : 100,
                            height : 100,
                            
                        });

                        </script>        

   