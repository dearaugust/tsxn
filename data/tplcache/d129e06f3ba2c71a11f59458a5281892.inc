<img src="[field:slide function=GetImgUrl(@me)/]" alt="[field:title/]" />
      <div class="fh5co-dynamic-main">
        <div class="container">
          <div class="row">
            <div class="col-md-8 col-md-offset-2 text-center fh5co-heading">
              <h2>[field:title/]
                <span>[field:typenamedir/]</span>
              </h2>
            </div>
            <div class="col-md-10 col-md-offset-1">[field:body function=cn_substr(html2text(@me),1000)/]</div>
          </div>
        </div>
      </div>