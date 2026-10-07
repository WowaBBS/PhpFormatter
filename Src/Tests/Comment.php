<? $argv[]=__FILE__; $LogFile=False; Include '../App.php8';
  // Check=[Start:String, Loop_Tab: Int, Loop:String, End_Tab:Int, End:String]

  // Key: TestComments

  // WFormat: Filter.Comment.DebugPos=True;
  // WFormat: Filter.Comment.DebugCheckInfo=True;

  // Single inline comment // Check='// '
   // Single inline comment2 // Check='// '
  
  # Single inline comment # Check='# '

  #Single inline comment # Check='#'
  
  // Multi inline comment //
  // Check='// '
  
  //   Multi inline comment shifted //
  //   Check='//   '
  
  // Multi inline comment //
  //Check='//'
  
  # Multi inline comment #
  # Second line
  # Check='# '

  # Multi inline comment #
  #Second line
  # Check='#'
  
  // TODO: /* */ Check='// '
  
  /* Inline comment * Check=["/* ", " *\\/"] */
  
  /*
    Multiline comment1
    Second line
    Check=["/*\n", 2, -1, "*\/"]
   */
  
  /* Multiline comment2
     Second line
     Check=['/* ', 3, -3, "*\/"]
  */
  
  /*
   * Multiline comment3
   * Second line
     Check=["/*\n", 1, 0, "*\/"]
   */
  
  /*
   * Multiline comment4
   * Second line
   * Check=["/*\n", 1, '* ', 0, "*\/"]
   */
  
  /*
   * Multiline comment5
   * Second line
   * Check=["/*\n", 1, '* ', -1, "*\/"]
  */
  
  /* Multiline comment6
   * Second line
   * Check=["/* ", 1, '* ', 0, "*\/"]
   */
  
  /* Multiline comment with fallen body
 Second line
 Check=["/* ", -1, "  *\/"]  */

  /**
   * Multiline document comment
   * Second line
   * Check=["/**\n", 1, '* ', 0, "*\/"]
   */

  /**
   * Multiline document comment with fallen end
   * Second line
   * Check=["/**\n", 1, '* ', -1, "*\/"]
  */

  /*
   * Multiline document comment with shifted end
   * Second line
   * Check=["/*\n", 1, '* ', 1, "*\/"]
    */

  /**
   * Multiline document comment with over fallen end
   * Second line
   * Check=["/**\n", 1, '* ', -3, "*\/"]
*/

  /**
* Multiline document comment with over fallen body
* Second line
* Check=["/**\n", -2, '* ', 0, "*\/"]
*/

  /**** Section1
   * Check=["/**** ", 1, '* ', 0, "*\/"]
   */

  /****
   * Section2
   * Check=["/****\n", 1, '* ', 0, "*\/"]
   */
