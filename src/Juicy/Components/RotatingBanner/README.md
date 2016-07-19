# Banner Component

Recommended for homepage banners or hero banners that rotate.

## Dependencies

- Bootstrap 3
- Cycle2 (http://jquery.malsup.com/cycle2/download/)
- Ensure your theme defines the `.abs`, `.rel` classes for absolute and relative positioning respectively.
- (Optional but recommended) add the `.trans-default` class to your theme that defines default transition timings.

## Extending

Want to define custom aspect ratios for your slides?
- Extend the Component class in your own theme, and set the variables `$defaultAspectRatio` and `$defaultMobileAspectRatio`.
- Register the extended class rather than the class from Juicy.
