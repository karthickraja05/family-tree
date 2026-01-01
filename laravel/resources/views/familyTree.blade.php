<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Family Tree</title>
    <link rel="stylesheet" href="/assets/style.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery (you already use it) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


  </head>
  <body>    

    <div class="header-row">
        <h2 class="title" id="title">My Family Tree</h2>
        <a href="/dashboard">
            <button>Go to Dashboard</button>
        </a>
    </div>
    
    <div class="tree">
      <!-- Generation 1 -->
      <div class="generation gen-1" id="parents_node">
        <div class="h-line"></div>
        <div class="node">Grandfather</div>
        <div class="node">Grandmother</div>
      </div>

      <div class="v-line" id="arrow1"></div>

      <!-- Generation 2 -->
      <div class="generation gen-2" id="current_node">
        <div class="h-line"></div>
        <div class="node">Uncle 1</div>
        <div class="node">Uncle 2</div>
        <div class="node highlight">Father</div>
        <div class="node">Aunt 1</div>
        <div class="node">Aunt 2</div>
      </div>

      <div class="v-line" id="arrow2"></div>

      <!-- Generation 4 -->
      <div class="generation gen-4" id="child_node">
        <div class="h-line"></div>
        <div class="node">Child 1</div>
        <div class="node">Child 2</div>
      </div>
    </div>

    <!-- MODAL -->
    <div class="modal d-none align-items-center justify-content-center" id="nodeModal">
        <div class="modal-content p-4 rounded shadow" style="max-width: 420px; width: 100%;">

            <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                <h5 class="mb-0" id="popup_name">xyz</h5>
                <span class="text-muted">|</span>
                <div id="view_tree">
                    <i class="fa fa-eye"></i>
                    <span>View Tree</span>
                </div>
            </div>

            <div class="info-box">
                <div class="info-row">
                    <span class="label">Gender</span>
                    <span class="value" id="popup_gender">Female</span>
                </div>
                <div class="info-row">
                    <span class="label">DOB</span>
                    <span class="value" id="popup_dob">2025-05-45</span>
                </div>
                <div class="info-row">
                    <span class="label">Wife / Husband</span>
                    <span class="value" id="popup_relation">-</span>
                </div>
                <div class="info-row">
                    <span class="label"></span>
                    <span class="value">
                        <div id="view_tree2" data-key="0">
                            <i class="fa fa-eye"></i>
                            <span>View Tree</span>
                        </div>
                    </span>
                </div>
            </div>


            <div class="mb-3">
                <label class="form-label">Relation</label>
                <select name="relation_add" id="relation_add" class="form-select">
                    <option value="">Select</option>
                    <option value="child">Child</option>
                    <option value="parent">Parent</option>
                    <option value="sibling">Sibling</option>
                    <option value="spouse">Wife/Husband</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Relation Name</label>
                <input type="text" name="add_name" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Date of Birth</label>
                <input type="text" name="dob" class="form-control" placeholder="YYYY" pattern="[0-9]{4}" maxlength="4" value="1990">
            </div>

            <div class="mb-4">
                <label class="form-label">Gender</label>
                <select name="gender" id="gender" class="form-select">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button id="saveBtn" class="btn btn-primary">Add</button>
                <button class="btn btn-secondary close-btn">Close</button>
            </div>
        </div>
    </div>
    <script>
        const BaseID = {{ $person->id }};
    </script>
    <script src="/assets/script.js"></script>
  </body>
</html>
