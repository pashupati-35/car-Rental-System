<div class="form-group row">
    <label class="col-sm-3 col-form-label">Car Number</label>
    <div class="col-sm-9">
        <div class="row">
            <!-- Provision Dropdown -->
            <div class="col mb-2">
                <select class="form-control" id="car_number_part1" name="car_number_part1" required>
                    <option value="">Select Provision</option>
                    <option value="koshi">Koshi</option>
                    <option value="madhesh">Madhesh</option>
                    <option value="bagmati">Bagmati</option>
                    <option value="lumbini">Lumbini</option>
                    <option value="gandaki">Gandaki</option>
                    <option value="karnali">Karnali</option>
                    <option value="sudhurpachhama">Sudhurpachhama</option>
                </select>
            </div>
            <!-- Lot Dropdown -->
            <div class="col mb-2">
                <select class="form-control" id="car_number_part2" name="car_number_part2" required>
                    <option value="">Select Lot</option>
                    @for ($i = 1; $i <= 99; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <!-- Model Dropdown -->
            <div class="col mb-2">
                <select class="form-control" id="car_number_part3" name="car_number_part3" required>
                    <option value="">Select Model</option>
                    <option value="ba">Ba</option>
                    <option value="ka">Ka</option>
                    <option value="gh">Gh</option>
                    <option value="sha">Sha</option>
                    <option value="ya">Ya</option>
                </select>
            </div>
            <!-- Four Digit Number Input -->
            <div class="col mb-2">
                <input type="number" class="form-control" id="car_number_part4" name="car_number_part4" placeholder="Four Digit Number" max="9999" required oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);">
            </div>
        </div>
    </div>
</div>
