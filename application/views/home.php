
                <div class="col-md-12">
                    <h1>{form}</h1>
                    
                    <div class="alert alert-warning response {hide-if-no-val-error}">Error: {validation_errors}</div>
                    <div class="alert alert-warning response {hide-if-no-error}">Error: {error}</div>
                    
                    <h3 id="response" class="{hide-if-no-key}">
                        <a href="{urlwithkey}" class="responseLink">{urlwithkey}</a>
                        <button class="btn btn-default" id="copy-button" type="button">Copy</button>
                    </h3>
                </div>