<?
  // Check=[Start:String, Loop:String, End:String, Tab:Int]
  
  // WFormat: Test=DebugPos;
  // WFormat: Filter.Comment.Test=True;
  // WFormat: CheckCommentDetect=True;

  // Single inline comment // Check=['// ', '// ', '', 2]
   // Single inline comment2 // Check=['// ', '// ', '', 3]
  
  # Single inline comment # Check=['# ', '# ', '', 2]

  #Single inline comment # Check=['#', '#', '', 2]
  
  // Multi inline comment //
  // Check=['// ', '// ', '', 2]
  
  //   Multi inline comment //
  //   Check=['//   ', '//   ', '', 2]
  
  // Multi inline comment //
  //Check=['//', '//', '', 2]
  
  # Multi inline comment #
  # Second line
  # Check=['# ', '# ', '', 2]

  # Multi inline comment #
  #Second line
  # Check=['#', '#', '', 2]
  
  /*
    Multiline comment1
    Second line
    Check=["/*\n", '  ', "\n *\/", 2]
   */
  
  /* Multiline comment1
     Second line
     Check=['/* ', '   ', "\n*\/", 2]
  */
  
  /*
   * Multiline comment2
   * Second line
     Check=["/*\n", ' ', "\n*\/", 2]
   */
  
  /*
   * Multiline comment3
   * Second line
   * Check=["/* ", ' * ', "\n*\/", 2]
   */
  
  /*
   * Multiline comment4
   * Second line
   * Check=["/* ", ' * ', "\n*\/", 2]
  */
  
  /* Multiline comment5
   * Second line
   * Check=["/* ", ' * ', "\n*\/", 2]
   */
  
  /* Multiline comment with fallen body
 Second line
 Check=["/* ", '', "  *\/", 1]  */

  /**
   * Multiline document comment
   * Second line
   * Check=["/**\n", ' * ', "\n*\/", 2]
   */

  /**
   * Multiline document comment with fallen end
   * Second line
   * Check=["/**\n", ' * ', "\n*\/", 2]
  */

  /*
   * Multiline document comment with shifted end
   * Second line
   * Check=["/**\n", ' ', "\n*\/", 2]
    */
  